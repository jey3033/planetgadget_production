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

abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * @var InsuranceFeeHelper
     */
    protected $_helper;

    /**
     * AbstractCalculation constructor.
     *
     * @param InsuranceFeeHelper $helper
     */
    public function __construct(InsuranceFeeHelper $helper)
    {
        $this->_helper = $helper;
    }
}