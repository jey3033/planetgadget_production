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
 * Class SyncRewardPointToErp
 */
class SyncRewardPointToErp
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Reward
     */
    protected $erpReward;

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
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     * @param \Kemana\MsDynamics\Model\Customer $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward,
        \Kemana\MsDynamics\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->helper = $helper;
        $this->erpReward = $erpReward;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
    }

    /**
     * @throws InputMismatchException
     * @throws InputException
     * @throws LocalizedException
     */
    public function syncRewardPointFromMagentoToErp()
    {   
        if (!$this->helper->isEnable()) {
            return;                                             
        }                                   
        $this->helper->log('REWARD POINT : Cron Start to Sync Reward Point To ERP', 'info');

        $syncCustomers = $this->customer->getSyncCustomersList();
        foreach($syncCustomers as $customer) {
            $customerId = $customer->getId();
            if ($customer->getCustomAttribute('ms_dynamic_customer_number')) {
                $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
                $storeId = $this->storeManager->getStore()->getId();

                $reward = $this->erpReward->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
                $magentoRewardPointBalance = (int)$reward->getPointsBalance();
                if($magentoRewardPointBalance) {
                    $this->erpReward->addCustomerEarnPointToErp($customerId, $erpCustomerNumber, $magentoRewardPointBalance);
                }
            }
        }
        $this->helper->log('REWARD POINT : Cron End to Sync Reward Point To ERP', 'info');
    }
}
