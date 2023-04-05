<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Kemana\Banner\Model\Config\Source
 */
class Type implements OptionSourceInterface
{
    /**
     * Const Image
     */
    const IMAGE = '0';

    /**
     * Const Content
     */
    const CONTENT = '1';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::IMAGE,
                'label' => __('Image')
            ],
            [
                'value' => self::CONTENT,
                'label' => __('Advanced')
            ]
        ];

        return $options;
    }
}
