<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Widget
 * @package Kemana\Banner\Block
 */
class Widget extends Slider
{
    /**
     * Function getBannerCollection
     * @return array|AbstractCollection
     */
    public function getBannerCollection()
    {
        try {
            $sliderId = $this->getData('slider_id');
            if (!$sliderId || !$this->helperData->isEnabled()) {
                return [];
            }

            $sliderCollection = $this->helperData->getActiveSliders();
            $slider = $sliderCollection->addFieldToFilter('slider_id', $sliderId)->getFirstItem();
            $this->setSlider($slider);

            return parent::getBannerCollection();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }
    }
}
