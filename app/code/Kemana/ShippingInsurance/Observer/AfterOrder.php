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

namespace Kemana\ShippingInsurance\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterOrder
 * @package Kemana\ShippingInsurance\Observer
 */
class AfterOrder implements ObserverInterface
{
    /**
     * Set payment insurance fee to order
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        $insuranceFee = $quote->getInsuranceFee();
        $baseinsuranceFee = $quote->getBaseInsuranceFee();
        if (!$insuranceFee || !$baseinsuranceFee) {
            return $this;
        }
        
        $order = $observer->getOrder();
        $order->setData('insurance_fee', $insuranceFee);
        $order->setData('base_insurance_fee', $baseinsuranceFee);

        return $this;
    }
}
