<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Catalog
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Catalog\Plugin\Catalog\Model;

/**
 * Class Config
 */
class Config
{

    /**
     * Retrieve Attributes Used for Sort by as array
     *
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $options)
    {
        return [
            'position' => __('Position'),
            'Lowest' => __('Price : Lowest'),
            'Highest' => __('Price : Highest'),
        ];
    }

}
