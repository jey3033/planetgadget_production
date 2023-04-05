<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Observer;

use Kemana\Banner\Block\Slider;
use Kemana\Banner\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

/**
 * Class AddBlock
 * @package Kemana\Banner\Observer
 */
class AddBlock implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * AddBlock constructor.
     *
     * @param RequestInterface $request
     * @param Data $helperData
     */
    public function __construct(
        RequestInterface $request,
        Data $helperData
    ) {
        $this->request = $request;
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->helperData->isEnabled()) {
            return $this;
        }

        $type = array_search($observer->getEvent()->getElementName(), [
            'header'           => 'header',
            'content'          => 'content',
            'page-top'         => 'page.top',
            'footer-container' => 'footer-container',
            'sidebar'          => 'catalog.leftnav'
        ], true);

        if ($type !== false) {
            /** @var Layout $layout */
            $layout = $observer->getEvent()->getLayout();
            $fullActionName = $this->request->getFullActionName();
            $output = $observer->getTransport()->getOutput();

            foreach ($this->helperData->getActiveSliders() as $slider) {
                $locations = explode(',', $slider->getLocation());
                foreach ($locations as $value) {
                    if (!empty($value)) {
                        list($pageType, $location) = explode('.', $value);
                        if (($fullActionName === $pageType || $pageType === 'allpage') &&
                            strpos($location, $type) !== false
                        ) {
                            $content = $layout->createBlock(Slider::class)
                                ->setSlider($slider)
                                ->toHtml();

                            if (strpos($location, 'top') !== false) {
                                $output = "<div id=\"kemana-bannerslider-block-before-{$type}-{$slider->getId()}\">
                                        $content</div>" . $output;
                            } else {
                                $output .= "<div id=\"kemana-bannerslider-block-after-{$type}-{$slider->getId()}\">
                                        $content</div>";
                            }
                        }
                    }
                }
            }

            $observer->getTransport()->setOutput($output);
        }

        return $this;
    }
}
