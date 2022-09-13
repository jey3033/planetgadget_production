<?php /** @noinspection PhpUndefinedMethodInspection */

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

namespace Kemana\ShippingInsurance\Model\Total;

use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Kemana\ShippingInsurance\Helper\Data as InsuranceFeeHelper;
use Kemana\ShippingInsurance\Model\Calculation\Calculator\CalculatorInterface;

/**
 * Class InsuranceFee
 * @package Kemana\ShippingInsurance\Model\Total
 */
class InsuranceFee extends Address\Total\AbstractTotal
{
    /**
     * @var InsuranceFeeHelper
     */
    protected $helper;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param InsuranceFeeHelper $helper
     * @param CalculatorInterface $calculator
     */
    public function __construct(InsuranceFeeHelper $helper, CalculatorInterface $calculator) {
        $this->calculator = $calculator;
        $this->helper = $helper;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        $insuranceFee = $this->calculator->calculate($quote);
        $total->setTotalAmount('insurance_fee', $insuranceFee);
        $total->setBaseTotalAmount('insurance_fee', $insuranceFee);
        $total->setInsuranceFee($insuranceFee);
        $total->setBaseInsuranceFee($insuranceFee);
        $quote->setInsuranceFee($insuranceFee);
        $quote->setBaseInsuranceFee($insuranceFee);
        $quote->setGrandTotal($total->getGrandTotal() + $insuranceFee);
        $quote->setBaseGrandTotal($total->getBaseGrandTotal() + $insuranceFee);
        
        return $this;
    }

    /**
     * @param Address\Total $total
     */
    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Address\Total $total): array
    {
        $result = [];
        $insuranceFee = $this->calculator->calculate($quote);

        if ($insuranceFee > 0.0) {
            $result = [
                'code' => 'insurance_fee',
                'title' => $this->getLabel(),
                'value' => $insuranceFee
            ];
        }

        return $result;
    }

    /**
     * Get label
     *
     * @return Phrase
     */
    public function getLabel(): Phrase
    {
        return __($this->helper->getTitle());
    }
}
