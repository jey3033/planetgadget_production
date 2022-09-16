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
        $this->helper->log('REWARD POINT : Start After Place an Order Event for Redeem Point', 'info');

        $order = $observer->getEvent()->getOrder();        
        $rewardPoint = 0;
        $orderId = $order->getId();
        $rewardPoint = $order->getRewardPointsBalance();  

        $erpCustomerNumber = '';
        $customerId = $order->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        if ($customerId && $customer->getCustomAttribute('ms_dynamic_customer_number')) {            
            $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
        }

        if ($erpCustomerNumber != '') {
            $dataToOrder = [
                "DocumentNo" => $order->getIncrementId(),
                "CustomerNo" => $erpCustomerNumber,
                "Description" => 'Redeem point',
                "Points" => $rewardPoint
            ];

            $dataToOrder = $this->helper->convertArrayToXml($dataToOrder);

            $redeemPointToErp = $this->erpReward->redeemRewardPointToErp($this->helper->redeemRewardPointFunction(),
                $this->helper->getSoapActionRedeemRewardPoint(), $dataToOrder);

            if ($redeemPointToErp['curlStatus'] == 500) {
                $this->helper->log($redeemPointToErp['response'], 'info');
            }

            if ($redeemPointToErp['curlStatus'] == 200 && isset($redeemPointToErp['response']['CustomerNo'])) {
                $this->helper->log('REWARD POINT : End After Place an Order Event successfully and customer ' . $customerId . ' redeem point sent to ERP', 'info');
            }
            $this->earnPointFromOrder($order, $customerId, $erpCustomerNumber);
        }
    }

    public function earnPointFromOrder($order, $customerId, $erpCustomerNumber)
    {
        $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));
        $pointsDelta = $this->rewardPointCounter->getPointsForRules($appliedRuleIds);

        $this->helper->log('REWARD POINT : Start After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
        
        if ($pointsDelta && !$order->getCustomerIsGuest()) {
            $this->erpReward->addCustomerEarnPointToErp($customerId, $erpCustomerNumber, (int)$pointsDelta);        
        }

        $this->helper->log('REWARD POINT : End After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
    }
}
