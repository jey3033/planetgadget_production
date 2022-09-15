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

namespace Kemana\ShippingInsurance\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

/**
 * Class InsuranceFee
 * @package Kemana\InsuranceFee\Model\Invoice\Total
 */
class InsuranceFee extends AbstractTotal
{
    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $invoice->setInsuranceFee(0);
        $invoice->setBaseInsuranceFee(0);
        $amount = $invoice->getOrder()->getInsuranceFee();
        $invoice->setInsuranceFee($amount);
        $amount = $invoice->getOrder()->getBaseInsuranceFee();
        $invoice->setBaseInsuranceFee($amount);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getInsuranceFee());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getInsuranceFee());

        return $this;
    }
}
