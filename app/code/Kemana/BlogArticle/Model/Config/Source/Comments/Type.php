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

namespace Kemana\Blog\Model\Config\Source\Comments;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 * @package Kemana\Blog\Model\Config\Source\Comments
 */
class Type implements ArrayInterface
{
    const DEFAULT_COMMENT = 1;
    const FACEBOOK = 2;
    const DISQUS = 3;
    const DISABLE = 4;

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
            self::DEFAULT_COMMENT => __('Default Comment'),
            self::DISQUS => __('Disqus Comment'),
            self::FACEBOOK => __('Facebook Comment'),
            self::DISABLE => __('Disable Completely')
        ];
    }
}
