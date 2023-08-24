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

namespace Kemana\Blog\Model\Config\Source\Comments\Facebook;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Orderby
 * @package Kemana\Blog\Model\Config\Source\Comments\Facebook
 */
class Orderby implements ArrayInterface
{
    const SOCIAL = 'social';
    const REVERSE_TIME = 'reverse_time';
    const TIME = 'time';

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
        return [self::SOCIAL => __('Social'), self::REVERSE_TIME => __('Reverse time'), self::TIME => __('Time')];
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
