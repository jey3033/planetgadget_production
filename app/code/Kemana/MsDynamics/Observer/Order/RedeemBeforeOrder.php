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

use Magento\Framework\Exception\PaymentException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RedeemBeforeOrder
 */
class RedeemBeforeOrder implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Reward\Observer\PlaceOrder\RestrictionInterface
     */
    protected $restriction;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Reward\Model\Reward\Balance\Validator
     */
    protected $validator;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     * @param \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Reward\Model\Reward\Balance\Validator $validator
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward,
        \Magento\Reward\Model\SalesRule\RewardPointCounter $rewardPointCounter,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Reward\Observer\PlaceOrder\RestrictionInterface $restriction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Reward\Model\Reward\Balance\Validator $validator
    )
    {
        $this->helper = $helper;
        $this->erpReward = $erpReward;
        $this->rewardPointCounter = $rewardPointCounter;
        $this->customerRepository = $customerRepository;
        $this->restriction = $restriction;
        $this->storeManager = $storeManager;
        $this->validator = $validator;
    }

    /**
     * Reduce reward points if points was used during checkout
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnable() && false == $this->restriction->isAllowed()) {
            return;
        }

        $this->helper->log('REWARD POINT : Start Before Place an Order Event for Redeem Point', 'info');
        $event = $observer->getEvent();
        /* @var $order \Magento\Sales\Model\Order */
        $order = $event->getOrder();
        /** @var $quote \Magento\Quote\Model\Quote $quote */
        $quote = $event->getQuote();
        /** @var  \Magento\Quote\Model\Quote\Address $address */
        $address = $event->getAddress();
        $customerId = $order->getCustomerId();
        $storeId = $this->storeManager->getStore()->getId();

        if ($customerId && $quote->getBaseRewardCurrencyAmount() > 0) {
            $rewardData = $quote->getIsMultiShipping() ? $address : $quote;
            $rewardPoint = $rewardData->getRewardPointsBalance();

            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getCustomAttribute('ms_dynamic_customer_number')) {            
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

                if (empty($redeemPointToErp)) {
                    $this->helper->log('REWARD POINT : ERP system might be off line', 'error');
                    return;
                }

                if ($redeemPointToErp['curlStatus'] == 500) {
                    $this->helper->log($redeemPointToErp['response'], 'info');
                    $responseData = $redeemPointToErp['response'];
                    $this->erpReward->getRewardModel()
                        ->setCustomerId($customerId)
                        ->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId())
                        ->setPointsDelta($rewardPoint)
                        ->setAction(\Magento\Reward\Model\Reward::REWARD_ACTION_REVERT)
                        ->setActionEntity($order)
                        ->updateRewardPoints();
                    throw new LocalizedException(__('REWARD POINT : You don\'t have enough reward points to pay for this purchase. Please remove reward point and try to place an order again'));                    
                    exit;
                }

                if ($redeemPointToErp['curlStatus'] == 200 && isset($redeemPointToErp['response']['CustomerNo'])) {
                    $this->helper->log('REWARD POINT : End Before Place an Order Event successfully and customer ' . $customerId . ' redeem point sent to ERP', 'info');
                }
            }
        }    
    }
}
