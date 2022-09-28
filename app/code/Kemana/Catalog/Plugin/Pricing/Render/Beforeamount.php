<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kemana\Catalog\Plugin\Pricing\Render;

use Magento\Framework\Pricing\Render\Amount;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Beforeamount
{
    protected $priceCurrency;

    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->priceCurrency = $priceCurrency;
    
    }


    public function beforeFormatCurrency(Amount $subject, $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION)
    {
        return [$amount, $includeContainer, 0];
    }
}