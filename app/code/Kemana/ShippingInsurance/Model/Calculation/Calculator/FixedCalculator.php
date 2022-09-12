<?php

/**
 * Copyright © 2017 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */
/**
 * @category Kemana
 * @package  Kemana_ShippingInsurance
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anupam Tiwari<anupam.tiwari@kemana.com>
 */

namespace Kemana\ShippingInsurance\Model\Calculation\Calculator;

use Kemana\ShippingInsurance\Helper\Data as InsuranceFeeHelper;
use Magento\Quote\Model\Quote;

class FixedCalculator extends AbstractCalculator
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * FixedCalculator constructor.
     * @param InsuranceFeeHelper $helper
     */
    public function __construct(InsuranceFeeHelper $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($helper);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(Quote $quote): float
    {
        $fee = 0.00;
        $method = $quote->getShippingAddress()->getShippingMethod();
        $shippingAddress = $quote->getShippingAddress();
        $subtotal = $quote->getBaseSubtotal();
        $thresholdValue = $this->scopeConfig->getValue('insurance_fee/general/threshold');
        if (isset($method) && $shippingAddress->getIsInsurance() && $subtotal >= $thresholdValue) {
            $fee = $quote->getSubtotal() * 0.0025;
            $fee = round($fee);
        }

        if (isset($method) && $shippingAddress->getExtensionAttributes()->getIsInsurance() && $subtotal >= $thresholdValue) {
            $fee = $quote->getSubtotal() * 0.0025;
            $fee = round($fee);
        }

        return $fee;
    }
}