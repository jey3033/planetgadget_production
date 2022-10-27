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
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\Quote $quote
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Quote $quote,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->quote = $quote;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInsuranceFeeForAnOrder() {
        $quote  = $this->checkoutSession->getQuote();

        $subTotal = (float)$quote->getSubtotal();

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $shippingMethodCode = null;

        $insurance = (float)$subTotal * 0.002;

        if ($shippingMethod) {
            $code = explode('_', $shippingMethod);

            if (isset($code[0]) && $code[0] == 'jne') {
                $shippingMethodCode = $code[0];
            }
        }

        if ($shippingMethodCode && $shippingMethodCode == "jne") {
            return $insurance + (float)$this->getInsuranceFixedAdminExtraFee();
        }

        return $insurance;

    }

    /**
     * @return mixed
     */
    public function getInsuranceIsEnable() {
        return $this->scopeConfig->getValue('insurance_fee/general/active');
    }

    /**
     * @return mixed
     */
    public function getInsuranceFixedAdminExtraFee() {
        return $this->scopeConfig->getValue('insurance_fee/general/extra_fee');
    }
}
