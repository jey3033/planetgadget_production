<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Controller\Adminhtml\Slider;

use Exception;
use Kemana\Banner\Controller\Adminhtml\Slider;
use Kemana\Banner\Model\SliderCronFactory;
use Kemana\Banner\Model\ResourceModel\SliderCron\CollectionFactory as SliderCronCollectionFactory;
use Kemana\Banner\Model\SliderFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use RuntimeException;
use Zend_Filter_Input;

/**
 * Class Save
 * @package Kemana\Banner\Controller\Adminhtml\Slider
 */
class Save extends Slider
{
    /**
     * JS helper
     *
     * @var Js
     */
    protected $jsHelper;

    /**
     * Date filter
     *
     * @var DateTime
     */
    protected $dateFilter;

    /**
     * @var SliderCronFactory
     */
    protected $sliderCron;

    /**
     * @var SliderCronCollectionFactory
     */
    protected $sliderCronCollection;

    /**
     * @var array
     */
    private $filterRules = [];

    /**
     * Save constructor.
     *
     * @param Js $jsHelper
     * @param SliderFactory $sliderFactory
     * @param Registry $registry
     * @param Context $context
     * @param DateTime $dateFilter
     * @param SliderCronFactory $sliderCron
     * @param SliderCronCollectionFactory $sliderCronCollection
     */
    public function __construct(
        Js $jsHelper,
        SliderFactory $sliderFactory,
        Registry $registry,
        Context $context,
        DateTime $dateFilter,
        SliderCronFactory $sliderCron,
        SliderCronCollectionFactory $sliderCronCollection
    ) {
        $this->jsHelper = $jsHelper;
        $this->dateFilter = $dateFilter;
        $this->sliderCron = $sliderCron;
        $this->sliderCronCollection = $sliderCronCollection;
        parent::__construct($sliderFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('slider')) {
            $data = $this->_filterData($this->getRequest()->getPost('slider'));
            $slider = $this->initSlider();

            $banners = $this->getRequest()->getPost('banners', -1);
            if ($banners != -1) {
                $slider->setBannersData($this->jsHelper->decodeGridSerializedInput($banners));
            }
            $slider->addData($data);

            $this->_eventManager->dispatch(
                'bannerslider_slider_prepare_save',
                [
                    'slider'  => $slider,
                    'request' => $this->getRequest()
                ]
            );

            try {
                $slider->save();

                $sliderCronCollection = $this->sliderCronCollection->create();
                $sliderCronCollection->addFieldToFilter('slider_id', $slider->getSliderId());
                $fromSliderId = null;
                $toSliderId = null;
                foreach ($sliderCronCollection as $sliderItem) {
                    if ($sliderItem->getFromTo() == '0') {
                        $fromSliderId = $sliderItem->getEntityId();
                    }
                    if ($sliderItem->getFromTo() == '1') {
                        $toSliderId = $sliderItem->getEntityId();
                    }
                }
                if ($data && isset($data['from_date']) && $data['from_date'] != '') {
                    $this->sliderCron->create()->load($fromSliderId)
                        ->setSliderId($slider->getSliderId())
                        ->setFromTo(0)
                        ->setScheduledAt($data['from_date'])
                        ->save();
                }
                if ($data && isset($data['to_date']) && $data['to_date'] != '') {
                    $this->sliderCron->create()->load($toSliderId)
                        ->setSliderId($slider->getSliderId())
                        ->setFromTo(1)
                        ->setScheduledAt($data['to_date'])
                        ->save();
                }

                $this->messageManager->addSuccess(__('The Slider has been saved.'));
                $this->_session->setKemanaBannerSliderSliderData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'bannerslider/*/edit',
                        [
                            'slider_id' => $slider->getId(),
                            '_current'  => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('bannerslider/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Slider.'));
            }

            $this->_getSession()->setKemanaBannerSliderSliderData($data);
            $resultRedirect->setPath(
                'bannerslider/*/edit',
                [
                    'slider_id' => $slider->getId(),
                    '_current'  => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('bannerslider/*/');

        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     *
     * @return array
     */
    protected function _filterData($data)
    {
        if (!empty($data['from_date'])) {
            $this->filterRules = ['from_date' => $this->dateFilter];
        }

        $inputFilter = new Zend_Filter_Input($this->filterRules, [], $data);
        $data = $inputFilter->getUnescaped();

        if (isset($data['responsive_items'])) {
            unset($data['responsive_items']['__empty']);
        }

        if ($this->getRequest()->getParam('banners')) {
            $data['banner_ids'] = $this->getRequest()->getParam('banners');
        }

        return $data;
    }
}
