<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Kemana\Banner\Model\SliderFactory;

/**
 * Class Slider
 * @package Kemana\Banner\Controller\Adminhtml
 */
abstract class Slider extends Action
{
    /**
     * Slider Factory
     *
     * @var SliderFactory
     */
    protected $sliderFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Slider constructor.
     *
     * @param SliderFactory $sliderFactory
     * @param Registry $coreRegistry
     * @param Context $context
     */
    public function __construct(
        SliderFactory $sliderFactory,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Slider
     *
     * @return \Kemana\Banner\Model\Slider
     */
    protected function initSlider()
    {
        $sliderId = (int)$this->getRequest()->getParam('slider_id');
        /** @var \Kemana\Banner\Model\Slider $slider */
        $slider = $this->sliderFactory->create();
        if ($sliderId) {
            $slider->load($sliderId);
        }
        $this->coreRegistry->register('bannerslider_slider', $slider);

        return $slider;
    }
}
