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

namespace Kemana\MsDynamics\Observer\Order;

/**
 * Class AfterPlaceOrder
 */
class AfterPlaceOrder implements \Magento\Framework\Event\ObserverInterface
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
     * @var RewardPointCounter
     */
    protected $rewardPointCounter;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     * @param \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward,
        \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->helper = $helper;
        $this->erpReward = $erpReward;
        $this->rewardPointCounter = $rewardPointCounter;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnable()) {
            return;
        }        

        $order = $observer->getEvent()->getOrder();        
        $orderId = $order->getId();
        $rewardPoint = $order->getRewardPointsBalance();  

        $erpCustomerNumber = '';
        $customerId = $order->getCustomerId();

        if ($customerId) {
            $this->helper->log('REWARD POINT : Start After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getCustomAttribute('ms_dynamic_customer_number')) {            
                $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
            }
            if ($erpCustomerNumber != '') {                
                $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));
                $pointsDelta = $this->rewardPointCounter->getPointsForRules($appliedRuleIds);
                
                if ($pointsDelta && !$order->getCustomerIsGuest()) {
                    $this->erpReward->addCustomerEarnPointToErp($customerId, $erpCustomerNumber, (int)$pointsDelta);        
                }
                $this->helper->log('REWARD POINT : End After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
            }
        }
    }
}
