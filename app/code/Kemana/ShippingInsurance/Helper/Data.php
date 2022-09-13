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

namespace Kemana\ShippingInsurance\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Kemana\ShippingInsurance\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @param $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue($config, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get module status
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return (bool) $this->getConfig('insurance_fee/general/active');
    }

    /**
     * Get insurance fee title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getConfig('insurance_fee/general/title');
    }

    /**
     * Get insurance fee amount
     *
     * @return float
     */
    public function getInsuranceFee(): float
    {
        return (float) $this->getConfig('insurance_fee/general/price');
    }

    /**
     * Get insurance fee price type
     *
     * @return int
     */
    public function getPriceType(): int
    {
        return (int) 0;
    }

    /**
     * Get module status
     *
     * @return bool
     */
    public function isRefund(): bool
    {
        return true;
    }

    /**
     *  Get insurance fee threshold
     * @return float
     */
    public function getThreshold(): float
    {
        return (float) $this->getConfig('insurance_fee/general/threshold');
    }
}