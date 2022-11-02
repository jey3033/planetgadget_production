<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Insurance
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Insurance\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepositoryInterface;

    /**
     * @var \Kemana\Insurance\Logger\Logger
     */
    protected $logger;

    protected $request;

    protected $orderFactory;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Kemana\Insurance\Logger\Logger $logger
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session                    $checkoutSession,
        \Magento\Quote\Model\Quote                         $quote,
        \Magento\Sales\Api\OrderRepositoryInterface        $orderRepositoryInterface,
        \Kemana\Insurance\Logger\Logger                    $logger,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        Context                                            $context
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->quote = $quote;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->logger = $logger;
        $this->request = $request;
        $this->context = $context;
        $this->orderFactory = $orderFactory;

        parent::__construct($context);
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInsuranceFeeForAnOrder($merchantOrderId = null)
    {
        if (!$this->getInsuranceIsEnable()) {
            return ['insurance' => 0, "subTotal" => 0];
        }

        $this->log('Hit the function getInsuranceFeeForAnOrder()');
        if ($merchantOrderId) {
            $this->log('Merchant Order ID ' . $merchantOrderId . ' load details from order repository');
            $order = $this->orderFactory->create()->loadByIncrementId($merchantOrderId);
            $subTotal = (float)$order->getSubtotal();
            $shippingMethod = $order->getShippingMethod();

        } else {
            $this->log('No order ID and getting details from quote');
            $quote = $this->checkoutSession->getQuote();
            $subTotal = (float)$quote->getSubtotal();
            $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        }

        $this->log('Order Sub total is ' . $subTotal);
        $this->log('Shipping method is ' . $shippingMethod);

        $shippingMethodCode = null;

        $insurance = (float)$subTotal * 0.002;

        $this->log('Calculated Insurance fee (without admin fee) is ' . $insurance);

        if ($shippingMethod) {
            $code = explode('_', $shippingMethod);

            $this->log('Shipping method array after exploding by _ ' . implode(" ", $code));

            if (isset($code[0]) && ($code[0] == 'jne' || $code[0] == 'jnt')) {
                $shippingMethodCode = $code[0];
            }
        }

        if ($shippingMethodCode && $shippingMethodCode == "jne") {
            $insurance = $insurance + (float)$this->getInsuranceFixedAdminExtraFee();
            $this->log('Shipping Method code is ' . $shippingMethodCode . ' and total insurance fee and admin fee is ' . $insurance);
            return ['insurance' => $insurance, "subTotal" => $subTotal];
        } else if ($shippingMethodCode && $shippingMethodCode == "jnt") {
            $this->log('Shipping Method code is ' . $shippingMethodCode . ' and total insurance fee (no admin fee) is ' . $insurance);
            return ['insurance' => $insurance, "subTotal" => $subTotal];
        } else {
            $this->log('Shipping Method code not equal to jne or j&t and insurance fee is 0');
            return ['insurance' => 0, "subTotal" => $subTotal];
        }
    }

    /**
     * @return mixed
     */
    public function getInsuranceIsEnable()
    {
        return $this->scopeConfig->getValue('insurance_fee/general/active');
    }

    /**
     * @return mixed
     */
    public function getInsuranceFixedAdminExtraFee()
    {
        return $this->scopeConfig->getValue('insurance_fee/general/extra_fee');
    }

    /**
     * @return mixed
     */
    public function getInsuranceLogIsEnable()
    {
        return $this->scopeConfig->getValue('insurance_fee/general/log');
    }

    /**
     * @param $message
     * @return void
     */
    public function log($message) {
        if ($this->getInsuranceLogIsEnable()) {
            $this->logger->info($message);
        }
    }
}
