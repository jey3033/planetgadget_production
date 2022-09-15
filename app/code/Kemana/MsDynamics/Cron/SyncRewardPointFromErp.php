<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Jalpa Patel <jalpa@kemana.com>
 */

namespace Kemana\MsDynamics\Cron;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Class SyncRewardPointFromErp
 */
class SyncRewardPointFromErp
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @var \Kemana\MsDynamics\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Kemana\MsDynamics\Model\Customer $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Kemana\MsDynamics\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws InputMismatchException
     * @throws InputException
     * @throws LocalizedException
     */
    public function syncRewardPointFromErpToMagento()
    {   
        if (!$this->helper->isEnable()) {
            return;
        }
        $this->helper->log('-- Cron Start to Get Reward Point From ERP --', 'info');
        $syncCustomers = $this->customer->getSyncCustomersList();

        foreach($syncCustomers as $customer) {
            $customerId = $customer->getId();
            $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
            $storeId = $this->storeManager->getStore()->getId();

            $reward = $this->helper->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
            $magentoRewardPointBalance = $reward->getPointsBalance();

            try {
                if ($erpCustomerNumber) {
                    $dataToGetRewardPoint = [
                        "customer_no" => $erpCustomerNumber
                    ];

                    $dataToGetRewardPoint = $this->helper->convertArrayToXml($dataToGetRewardPoint);
                    $getRewardPoint = $this->erpCustomer->getRewardPointFromErp($this->helper->getRewardPointFunction(),
                        $this->helper->getSoapActionGetRewardPoint(), $dataToGetRewardPoint);

                    if (empty($getRewardPoint)) {
                        $this->helper->log('ERP system might be off line', 'error');
                        return;
                    }

                    if (isset($getRewardPoint['curlStatus']) == '200') {
                        if($getRewardPoint['response']['PointBalance'] !== $magentoRewardPointBalance) {
                            $this->helper->getRewardModel()
                                ->setCustomerId($customerId)
                                ->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId())
                                ->setPointsDelta($getRewardPoint['response']['PointBalance'])
                                ->setAction(\Kemana\MsDynamics\Model\Reward::REWARD_ACTION_FOR_ERP)
                                ->setActionEntity($customer)
                                ->updateRewardPoints();
                        }

                        $this->helper->log('Cron Customer ERP Number ' . $erpCustomerNumber . ' Updated points from ERP. Magento ID ' . $customerId, 'info');
                        $this->helper->log('-- Cron End to Get Reward Point From ERP --', 'info');
                    }
                }
            } catch (\Exception $e) {
                $this->helper->log('Exception : Unable to update reward point for EPR customer number ' . $erpCustomerNumber['CustomerNo'] . ' in Magento. Error : ' . $e->getMessage(), 'error');
            }
        }        
    }
}
