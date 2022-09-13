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
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var RewardPointCounter
     */
    protected $rewardPointCounter;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->_orderFactory = $orderFactory;
        $this->customer = $customer;
        $this->rewardPointCounter = $rewardPointCounter;
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
        $this->helper->log('Start After Place an Order Event', 'info');

        $orderIds = $observer->getEvent()->getOrderIds();
        
        $rewardPoint = 0;
        $orderId = $orderIds[0];
        //$orderId = 135;            
        $order = $this->_orderFactory->create()->load($orderId);
        $rewardPoint = $order->getRewardPointsBalance();  

        $customerId = $order->getCustomerId();
        //$customerId = 1442;
        $customer = $this->customer->load($customerId);
        $erpCustomerNumber = $customer->getMsDynamicCustomerNumber();

        $customer = $observer->getEvent()->getCustomer();

        $dataToOrder = [
            "DocumentNo" => $order->getIncrementId(),
            "CustomerNo" => $erpCustomerNumber,
            "Description" => 'Redeem point',
            "Points" => $rewardPoint
        ];

        $dataToOrder = $this->helper->convertArrayToXml($dataToOrder);

        $redeemPointToErp = $this->erpCustomer->redeemRewardPointToErp($this->helper->redeemRewardPointFunction(),
            $this->helper->getSoapActionRedeemRewardPoint(), $dataToOrder);

        if ($redeemPointToErp['curlStatus'] == 500) {
            $this->helper->log($redeemPointToErp['response'], 'info');
        }

        if ($redeemPointToErp['curlStatus'] == 200 && isset($redeemPointToErp['response']['CustomerNo'])) {
            $this->helper->log('End After Place an Order Event successfully and customer ' . $customerId . ' redeem point sent to ERP', 'info');
        }
        $this->earnPointFromOrder($order, $customerId, $erpCustomerNumber);
    }

    public function earnPointFromOrder($order, $customerId, $erpCustomerNumber)
    {
        $appliedRuleIds = array_unique(explode(',', $order->getAppliedRuleIds()));
        $pointsDelta = $this->rewardPointCounter->getPointsForRules($appliedRuleIds);

        $this->helper->log('Start After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
        
        if ($pointsDelta && !$order->getCustomerIsGuest()) {
            $this->helper->addCustomerEarnPointToErp($customerId, $erpCustomerNumber, $this->erpCustomer, (int)$pointsDelta);        
        }

        $this->helper->log('End After Place an Order Earn Reward Point for customer ' . $customerId, 'info');
    }
}
