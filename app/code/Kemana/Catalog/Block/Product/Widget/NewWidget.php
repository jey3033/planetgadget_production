<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kemana\Catalog\Block\Product\Widget;

/**
 * New products widget
 */
class NewWidget extends \Magento\Catalog\Block\Product\NewProduct
{

    const TITILE = 'New Products';

    public function getTitle()
    {
        if (!$this->hasData('title')) {
            $this->setData('title', self::TITILE);
        }
        return $this->getData('title');
    }
}