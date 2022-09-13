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
 * Class SyncCustomersFromErp
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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customer;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $rewardFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customer = $customer;
        $this->rewardFactory = $rewardFactory;
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

        $this->helper->log('-- Cron Get Reward Point From ERP Start --.', 'info');

        $customerCollection = $this->getCustomerCollection();

        foreach($customerCollection as $customer) {
            $customerId = $customer->getId();
            $erpCustomerNumber = $customer->getMsDynamicCustomerNumber();
            $storeId = $this->storeManager->getStore()->getId();

            $reward = $this->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
            $magentoRewardPointBalance =  $reward->getPointsBalance();

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
                        $this->getRewardModel()
                            ->setCustomerId($customerId)
                            ->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId())
                            ->setPointsDelta($getRewardPoint['response']['PointBalance'])
                            ->setAction(\Kemana\MsDynamics\Model\Reward::REWARD_ACTION_FOR_ERP)
                            ->setActionEntity($customer)
                            ->updateRewardPoints();
                    }

                    $this->helper->log('Cron Customer ERP Number ' . $erpCustomerNumber . ' Updated points from ERP. Magento ID ' . $customerId, 'info');
                    $this->helper->log('-- Cron Get Reward Point From ERP End --', 'info');
                }
            }
        }        
    }

    /**
     * Get reward model
     *
     * @return \Magento\Reward\Model\Reward
     * @codeCoverageIgnore
     */
    protected function getRewardModel()
    {
        return $this->rewardFactory->create();
    }

    /**
     * Get Customer Collection
     *
     * @return \Magento\Customer\Model\Customer
     * @codeCoverageIgnore
     */
    public function getCustomerCollection() {
        return $this->customer->getCollection()
                ->addAttributeToFilter("ms_dynamic_customer_number", array("neq" => ""))
                ->load();
    }
}
