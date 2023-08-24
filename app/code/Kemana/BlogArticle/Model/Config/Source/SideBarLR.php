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

namespace Kemana\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SideBarLR
 * @package Kemana\Blog\Model\Config\Source
 */
class SideBarLR implements ArrayInterface
{
    const LEFT = '2columns-left';
    const RIGHT = '2columns-right';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->toArray() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::LEFT => __('Left'),
            self::RIGHT => __('Right')
        ];
    }
}
