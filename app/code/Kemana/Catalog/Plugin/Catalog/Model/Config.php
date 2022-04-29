<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
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
     * key = code, value = name
     *
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $options)
    {
        $options =  [
            'position' => __('Position'),
            'low_to_high' => __('Price : Lowest'),
            'high_to_low' => __('Price : Highest'),
        ];

        return $options;
    }

}
