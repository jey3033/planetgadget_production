<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Blog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   kemana team <jakartateam@kemana.com>
 */

namespace Kemana\Blog\Model\Config\Source\DateFormat;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Kemana\Blog\Model\Config\Source\DateFormat
 */
class Type implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */

    public function toOptionArray()
    {
        $dateArray = [];
        $type = [
            'F j, Y',
            'Y-m-d',
            'm/d/Y',
            'd/m/Y',
            'F j, Y g:i a',
            'F j, Y g:i A',
            'Y-m-d g:i a',
            'Y-m-d g:i A',
            'd/m/Y g:i a',
            'd/m/Y g:i A',
            'm/d/Y H:i',
            'd/m/Y H:i',
        ];
        foreach ($type as $item) {
            $dateArray[] = [
                'value' => $item,
                'label' => $item . ' (' . date($item) . ')'
            ];
        }

        return $dateArray;
    }
}
