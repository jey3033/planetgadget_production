<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kredivo\Payment\Model;

/**
 * Pay In Store payment method model
 */
class KredivoPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'kredivopayment';

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if (!$this->getConfigData('server_key')) {
            return false;
        }

        return parent::isAvailable($quote);
    }
}
