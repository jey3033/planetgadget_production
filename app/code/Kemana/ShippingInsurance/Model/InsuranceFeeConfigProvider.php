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

namespace Kemana\ShippingInsurance\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Kemana\ShippingInsurance\Helper\Data as InsuranceFeeHelper;
use Kemana\ShippingInsurance\Model\Calculation\Calculator\CalculatorInterface;

/**
 * Class InsuranceFeeConfigProvider
 * @package Kemana\ShippingInsurance\Model
 */
class InsuranceFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var InsuranceFeeHelper
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param InsuranceFeeHelper $helper
     * @param Session $checkoutSession
     * @param CalculatorInterface $calculator
     */
    public function __construct(InsuranceFeeHelper $helper, Session $checkoutSession, CalculatorInterface $calculator) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->calculator = $calculator;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $insuranceFeeConfig = [];
        $quote = $this->checkoutSession->getQuote();
        $insuranceFee = $this->calculator->calculate($quote);

        $insuranceFeeConfig['insurance'] = $this->helper->isEnable();
        $insuranceFeeConfig['insurance_fee_title'] = $this->helper->getTitle();
        $insuranceFeeConfig['insurance_fee_amount'] = $insuranceFee;
        $insuranceFeeConfig['insurance_fee_threshold'] = $this->helper->getThreshold();
        $insuranceFeeConfig['show_hide_insurance_fee'] = $insuranceFee > 0.0;
        return $insuranceFeeConfig;
    }
}
