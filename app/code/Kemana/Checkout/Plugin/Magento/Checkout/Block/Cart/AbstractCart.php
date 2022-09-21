<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Checkout
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Checkout\Plugin\Magento\Checkout\Block\Cart;

use Kemana\AcceleratorBase\ViewModel\QtyConfig;
use Magento\Framework\App\Request\Http;

/**
 * Class AbstractCart
 * @package Kemana\Checkout\Plugin\Magento\Checkout\Block\Cart
 */
class AbstractCart
{
    /**
     * @var QtyConfig
     */
    protected $qtyConfig;

    /**
     * @var QtyConfig
     */
    protected $request;

    /**
     * AbstractCart constructor.
     * @param QtyConfig $qtyConfig
     * @param Http $request
     */
    public function __construct(
        QtyConfig $qtyConfig,
        Http $request
    ) {
        $this->qtyConfig = $qtyConfig;
        $this->request = $request;
    }

    /**
     * Set custom template
     * @param \Magento\Checkout\Block\Cart\AbstractCart $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
    {
        $fullActionName= $this->request->getFullActionName();
       
        if ($this->qtyConfig->isEnableQtyDropDown()) {
            if ($fullActionName !== 'magento_giftregistry_index_items') 
            {
                $result->setTemplate('Kemana_AcceleratorBase::checkout/cart/item/default.phtml');
            }
        }
        return $result;
    }
}
