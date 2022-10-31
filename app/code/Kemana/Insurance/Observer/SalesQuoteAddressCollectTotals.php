<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */


namespace Kemana\Insurance\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


class SalesQuoteAddressCollectTotals implements ObserverInterface
{
    protected $insurancepath = 'insurance_fee/general/';

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

	public function execute(EventObserver $observer)
    {
        if($this->getConfigValue("active")){

            $quote  = $observer->getQuote();
            $total  = $observer->getTotal();
            $method = $quote->getShippingAddress()->getShippingMethod();

            if(isset($method) && (strpos($method, "jnt_") === 0 || strpos($method, "jne_") === 0)){
                $fee = $quote->getSubtotal() * 0.002;
                if(strpos($method, "jne_") === 0){
                    $extra_fee = $this->getConfigValue("extra_fee") ? $this->getConfigValue("extra_fee") : 0;
                    $fee = $fee + $extra_fee;
                }
                $amount = $total->getGrandTotal();
                $total->setShippingAmount($total->getShippingAmount() + $fee);
                $total->setGrandTotal($amount + $fee);
                $total->setBaseGrandTotal($amount + $fee);
            }
        }
    }

    public function getConfigValue($subpath)
    {
       return $this->scopeConfig->getValue($this->insurancepath.$subpath);
    }
}
