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
 * Class Effect
 * @package Kemana\Banner\Model\Config\Source
 */
class Effect implements OptionSourceInterface
{
    /**
     * Const Slider
     */
    const SLIDER = 'slider';

    /**
     * Const Fade out
     */
    const FADE_OUT = 'fadeOut';

    /**
     * Const Rotate Out
     */
    const ROTATE_OUT = 'rotateOut';

    /**
     * Const Flip out
     */
    const FLIP_OUT = 'flipOutX';

    /**
     * Const Roll Out
     */
    const ROLL_OUT = 'rollOut';

    /**
     * Const Zoom Out
     */
    const ZOOM_OUT = 'zoomOut';

    /**
     * Const Slider Out Left
     */
    const SLIDER_OUT_LEFT = 'slideOutLeft';

    /**
     * Const Slider out Right
     */
    const SLIDER_OUT_RIGHT = 'slideOutRight';

    /**
     * Const Light Speed Out
     */
    const LIGHT_SPEED_OUT = 'lightSpeedOut';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SLIDER,
                'label' => __('No')
            ],
            [
                'value' => self::FADE_OUT,
                'label' => __('fadeOut')
            ],
            [
                'value' => self::ROTATE_OUT,
                'label' => __('rotateOut')
            ],
            [
                'value' => self::FLIP_OUT,
                'label' => __('flipOut')
            ],
            [
                'value' => self::ROLL_OUT,
                'label' => __('rollOut')
            ],
            [
                'value' => self::ZOOM_OUT,
                'label' => __('zoomOut')
            ],
            [
                'value' => self::SLIDER_OUT_LEFT,
                'label' => __('slideOutLeft')
            ],
            [
                'value' => self::SLIDER_OUT_RIGHT,
                'label' => __('slideOutRight')
            ],
            [
                'value' => self::LIGHT_SPEED_OUT,
                'label' => __('lightSpeedOut')
            ],
        ];

        return $options;
    }
}
