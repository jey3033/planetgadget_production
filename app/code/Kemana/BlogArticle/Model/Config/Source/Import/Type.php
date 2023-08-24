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

namespace Kemana\Blog\Model\Config\Source\Import;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Kemana\Blog\Model\Config\Source\Import
 */
class Type implements ArrayInterface
{
    const WORDPRESS = "wordpress";
    const AHEADWORK = "aheadworksm1";
    const MAGEFAN = "magefan";

    /**
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
            "" => __('-- Please Select --'),
            self::WORDPRESS => __('Wordpress'),
            self::AHEADWORK => __('AheadWorks Blog [Magento 1]'),
            self::MAGEFAN => __('MageFan Blog [Magento 2]')
        ];
    }
}
