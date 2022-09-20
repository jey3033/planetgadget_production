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