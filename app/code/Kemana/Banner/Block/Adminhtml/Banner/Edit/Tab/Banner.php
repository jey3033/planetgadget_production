<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab;

use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render\Image as BannerImage;
use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render\Slider;
use Kemana\Banner\Helper\Data as HelperData;
use Kemana\Banner\Helper\Image as HelperImage;
use Kemana\Banner\Model\Config\Source\Template;
use Kemana\Banner\Model\Config\Source\Type;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Renderer\Snippet;

/**
 * Class Banner
 * @package Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab
 */
class Banner extends Generic implements TabInterface
{
    /**
     * Type options
     *
     * @var Type
     */
    protected $typeOptions;

    /**
     * Template options
     *
     * @var Template
     */
    protected $template;

    /**
     * Status options
     *
     * @var Enabledisable
     */
    protected $statusOptions;

    /**
     * @var HelperImage
     */
    protected $imageHelper;

    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Banner constructor.
     *
     * @param Type $typeOptions
     * @param Template $template
     * @param Enabledisable $statusOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperImage $imageHelper
     * @param WysiwygConfig $wysiwygConfig
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Type $typeOptions,
        Template $template,
        Enabledisable $statusOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperImage $imageHelper,
        WysiwygConfig $wysiwygConfig,
        HelperData $helperData,
        array $data = []
    ) {
        $this->typeOptions = $typeOptions;
        $this->template = $template;
        $this->statusOptions = $statusOptions;
        $this->imageHelper = $imageHelper;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Kemana\Banner\Model\Banner $banner */
        $banner = $this->_coreRegistry->registry('bannerslider_banner');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Banner Information'),
            'class'  => 'fieldset-wide'
        ]);

        if ($banner->getId()) {
            $fieldset->addField(
                'banner_id',
                'hidden',
                ['name' => 'banner_id']
            );
        }

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->statusOptions->toOptionArray(),
        ]);

        $typeBanner = $fieldset->addField('type', 'select', [
            'name'   => 'type',
            'label'  => __('Type'),
            'title'  => __('Type'),
            'values' => $this->typeOptions->toOptionArray(),
        ]);

        $uploadBanner = $fieldset->addField('image', BannerImage::class, [
            'name'  => 'image',
            'label' => __('Upload Image'),
            'title' => __('Upload Image'),
            'path'  => $this->imageHelper->getBaseMediaPath(HelperImage::TEMPLATE_MEDIA_TYPE_BANNER)
        ]);

        $uploadMobileBanner = $fieldset->addField('image_mobile', BannerImage::class, [
            'name'  => 'image_mobile',
            'label' => __('Upload Mobile Image'),
            'title' => __('Upload Mobile Image'),
            'path'  => $this->imageHelper->getBaseMediaPath(HelperImage::TEMPLATE_MEDIA_TYPE_BANNER)
        ]);

        $fromDateBanner = $fieldset->addField('from_date', 'date', [
            'name'         => 'from_date',
            'label'        => __('Display from'),
            'title'        => __('Display from'),
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'time_format' => $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT),
            'class' => 'validate-date'
        ]);

        $toDateBanner = $fieldset->addField('to_date', 'date', [
            'name'         => 'to_date',
            'label'        => __('Display to'),
            'title'        => __('Display to'),
            'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'time_format' => $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT),
            'class' => 'validate-date'
        ]);

        $titleBanner = $fieldset->addField('title', 'text', [
            'name'  => 'title',
            'label' => __('Banner title'),
            'title' => __('Banner title'),
        ]);

        $urlBanner = $fieldset->addField('url_banner', 'text', [
            'name'  => 'url_banner',
            'label' => __('Url'),
            'title' => __('Url'),
        ]);

        $newTab = $fieldset->addField('newtab', 'select', [
            'name'   => 'newtab',
            'label'  => __('Open new tab after click'),
            'title'  => __('Open new tab after click'),
            'values' => $this->statusOptions->toOptionArray(),
            'note'   => __('Automatically open new tab after clicking on the banner')

        ]);

        if (!$banner->getId()) {
            $defaultImage = array_values($this->helperData->unserialize($this->template->getImageUrls()))[0];
            $demoTemplate = $fieldset->addField('default_template', 'select', [
                'name'   => 'default_template',
                'label'  => __('Demo template'),
                'title'  => __('Demo template'),
                'values' => $this->template->toOptionArray(),
                'note'   => '<img src="' . $defaultImage . '" alt="demo"  class="article_image" id="demo-image">'
            ]);

            $insertVariableButton = $this->getLayout()->createBlock(Button::class, '', [
                'data' => [
                    'type'  => 'button',
                    'label' => __('Load Template'),
                ]
            ]);
            $insertButton = $fieldset->addField('load_template', 'note', [
                'text'  => $insertVariableButton->toHtml(),
                'label' => ''
            ]);
        }

        $content = $fieldset->addField('content', 'editor', [
            'name'     => 'content',
            'required' => false,
            'config'   => $this->wysiwygConfig->getConfig([
                'hidden'         => true,
                'add_variables'  => false,
                'add_widgets'    => false,
                'add_directives' => true
            ])
        ]);
        $subfieldset = $form->addFieldset('sub_fieldset', [
            'legend' => __('How to add slider content and images in correct way'),
            'class'  => 'fieldset-wide'
        ]);
        $subfieldset->addField('snippet', Snippet::class, [
            'name'  => 'snippet',
            'label' => __('Html Structure'),
            'title' => __('Html Structure'),
        ]);
        $fieldset->addField('sliders_ids', Slider::class, [
            'name'  => 'sliders_ids',
            'label' => __('Sliders'),
            'title' => __('Sliders'),
        ]);
        if (!$banner->getSlidersIds()) {
            $banner->setSlidersIds($banner->getSliderIds());
        }

        $bannerData = $this->_session->getData('bannerslider_banner_data', true);
        if ($bannerData) {
            $banner->addData($bannerData);
        } else {
            if (!$banner->getId()) {
                $banner->addData($banner->getDefaultValues());
            }
        }

        $dependencies = $this->getLayout()->createBlock(Dependence::class)
            ->addFieldMap($typeBanner->getHtmlId(), $typeBanner->getName())
            ->addFieldMap($urlBanner->getHtmlId(), $urlBanner->getName())
            ->addFieldMap($uploadBanner->getHtmlId(), $uploadBanner->getName())
            ->addFieldMap($uploadMobileBanner->getHtmlId(), $uploadMobileBanner->getName())
            ->addFieldMap($fromDateBanner->getHtmlId(), $titleBanner->getName())
            ->addFieldMap($toDateBanner->getHtmlId(), $titleBanner->getName())
            ->addFieldMap($titleBanner->getHtmlId(), $titleBanner->getName())
            ->addFieldMap($newTab->getHtmlId(), $newTab->getName())
            ->addFieldMap($content->getHtmlId(), $content->getName())
            ->addFieldMap($content->getHtmlId(), $content->getName())
            ->addFieldMap($subfieldset->getHtmlId(), $subfieldset->getName())
            ->addFieldDependence($urlBanner->getName(), $typeBanner->getName(), '0')
            ->addFieldDependence($uploadBanner->getName(), $typeBanner->getName(), '0')
            ->addFieldDependence($uploadMobileBanner->getName(), $typeBanner->getName(), '0')
            ->addFieldDependence($titleBanner->getName(), $typeBanner->getName(), '0')
            ->addFieldDependence($newTab->getName(), $typeBanner->getName(), '0')
            ->addFieldDependence($content->getName(), $typeBanner->getName(), '1')
            ->addFieldDependence($subfieldset->getName(), $typeBanner->getName(), '1')
        ;

        if (!$banner->getId()) {
            $dependencies->addFieldMap($demoTemplate->getHtmlId(), $demoTemplate->getName())
                ->addFieldMap($insertButton->getHtmlId(), $insertButton->getName())
                ->addFieldDependence($demoTemplate->getName(), $typeBanner->getName(), '1')
                ->addFieldDependence($insertButton->getName(), $typeBanner->getName(), '1');
        }

        // define field dependencies
        $this->setChild('form_after', $dependencies);

        $form->addValues($banner->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
