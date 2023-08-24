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

namespace Kemana\Blog\Block\Adminhtml\Post\Edit\Tab;

use DateTimeZone;
use Exception;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Topic;
use Kemana\Blog\Helper\Data as HelperData;
use Kemana\Blog\Helper\Image;
use Kemana\Blog\Model\Config\Source\Author;
use Kemana\Blog\Model\Config\Source\AuthorStatus;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Cms\Model\Page\Source\PageLayout as BasePageLayout;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Design\Robots;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;

/**
 * Class Post
 * @package Kemana\Blog\Block\Adminhtml\Post\Edit\Tab
 */
class Post extends Generic implements TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var Config
     */
    public $wysiwygConfig;

    /**
     * Country options
     *
     * @var Yesno
     */
    public $booleanOptions;

    /**
     * @var Robots
     */
    public $metaRobotsOptions;

    /**
     * @var Store
     */
    public $systemStore;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var BasePageLayout
     */
    protected $_layoutOptions;

    /**
     * @var Author
     */
    protected $_author;

    /**
     * @var AuthorStatus
     */
    protected $_status;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Session $authSession
     * @param DateTime $dateTime
     * @param BasePageLayout $layoutOption
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Yesno $booleanOptions
     * @param Robots $metaRobotsOptions
     * @param Store $systemStore
     * @param Image $imageHelper
     * @param Author $author
     * @param AuthorStatus $status
     * @param HelperData $helperData,
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Session $authSession,
        DateTime $dateTime,
        BasePageLayout $layoutOption,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Yesno $booleanOptions,
        Robots $metaRobotsOptions,
        Store $systemStore,
        Image $imageHelper,
        Author $author,
        AuthorStatus $status,
        HelperData $helperData,
        array $data = []
    ) {
        $this->wysiwygConfig     = $wysiwygConfig;
        $this->booleanOptions    = $booleanOptions;
        $this->metaRobotsOptions = $metaRobotsOptions;
        $this->systemStore       = $systemStore;
        $this->authSession       = $authSession;
        $this->_date             = $dateTime;
        $this->_layoutOptions    = $layoutOption;
        $this->imageHelper       = $imageHelper;
        $this->_author           = $author;
        $this->_status           = $status;
        $this->helperData        = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _prepareForm()
    {
        /** @var \Kemana\Blog\Model\Post $post */
        $post = $this->_coreRegistry->registry('kemana_blog_post');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('post_');
        $form->setFieldNameSuffix('post');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Post Information'),
            'class'  => 'fieldset-wide',
        ]);

        if ($this->_request->getParam('duplicate')) {
            $fieldset->addField('duplicate', 'hidden', [
                'name'  => 'duplicate',
                'value' => 1,
            ]);
        }
        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true,
        ]);
        $fieldset->addField('author_id', 'select', [
            'name'     => 'author_id',
            'label'    => __('Author'),
            'title'    => __('Author'),
            'required' => true,
            'values'   => $this->_author->toOptionArray(),
        ]);
        $fieldset->addField('enabled', 'select', [
            'name'   => 'enabled',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->_status->toOptionArray(),
        ]);
        if (!$post->hasData('enabled')) {
            $post->setEnabled(1);
        }

        $fieldset->addField('short_description', 'textarea', [
            'name'  => 'short_description',
            'label' => __('Short Description'),
            'title' => __('Short Description'),
        ]);
        $fieldset->addField('post_content', 'editor', [
            'name'   => 'post_content',
            'label'  => __('Content'),
            'title'  => __('Content'),
            'config' => $this->wysiwygConfig->getConfig([
                'add_variables'  => false,
                'add_widgets'    => true,
                'add_directives' => true,
            ]),
        ]);

        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId(),
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name'   => 'store_ids',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
            ])->setRenderer($rendererBlock);

            if (!$post->hasData('store_ids')) {
                $post->setStoreIds(0);
            }
        }

        $fieldset->addField('image', \Kemana\Blog\Block\Adminhtml\Renderer\Image::class, [
            'name'  => 'image',
            'label' => __('Image'),
            'title' => __('Image'),
            'path'  => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_POST),
            'note'  => __('The appropriate size is 265px * 250px.'),
        ]);
        $fieldset->addField('categories_ids', Category::class, [
            'name'  => 'categories_ids',
            'label' => __('Categories'),
            'title' => __('Categories'),
        ]);
        if (!$post->getCategoriesIds()) {
            $post->setCategoriesIds($post->getCategoryIds());
        }

        $fieldset->addField('topics_ids', Topic::class, [
            'name'  => 'topics_ids',
            'label' => __('Topics'),
            'title' => __('Topics'),
        ]);
        if (!$post->getTopicsIds()) {
            $post->setTopicsIds($post->getTopicIds());
        }

        $fieldset->addField('tags_ids', Tag::class, [
            'name'  => 'tags_ids',
            'label' => __('Tags'),
            'title' => __('Tags'),
        ]);
        if (!$post->getTagsIds()) {
            $post->setTagsIds($post->getTagIds());
        }

        $fieldset->addField('in_rss', 'select', [
            'name'   => 'in_rss',
            'label'  => __('In RSS'),
            'title'  => __('In RSS'),
            'values' => $this->booleanOptions->toOptionArray(),
        ]);
        $fieldset->addField('allow_comment', 'select', [
            'name'   => 'allow_comment',
            'label'  => __('Allow Comment'),
            'title'  => __('Allow Comment'),
            'values' => $this->booleanOptions->toOptionArray(),
        ]);
        $fieldset->addField(
            'publish_date',
            'date',
            [
                'name'        => 'publish_date',
                'label'       => __('Publish Date'),
                'title'       => __('Publish Date'),
                'date_format' => 'yyyy-MM-dd',
                'timezone'    => false,
                'time_format' => 'hh:mm:ss',
            ]
        );

        $seoFieldset = $form->addFieldset('seo_fieldset', [
            'legend' => __('Search Engine Optimization'),
            'class'  => 'fieldset-wide',
        ]);
        $seoFieldset->addField('url_key', 'text', [
            'name'  => 'url_key',
            'label' => __('URL Key'),
            'title' => __('URL Key'),
        ]);
        $seoFieldset->addField('meta_title', 'text', [
            'name'  => 'meta_title',
            'label' => __('Meta Title'),
            'title' => __('Meta Title'),
        ]);
        $seoFieldset->addField('meta_description', 'textarea', [
            'name'  => 'meta_description',
            'label' => __('Meta Description'),
            'title' => __('Meta Description'),
        ]);
        $seoFieldset->addField('meta_keywords', 'textarea', [
            'name'  => 'meta_keywords',
            'label' => __('Meta Keywords'),
            'title' => __('Meta Keywords'),
        ]);
        $seoFieldset->addField('meta_robots', 'select', [
            'name'   => 'meta_robots',
            'label'  => __('Meta Robots'),
            'title'  => __('Meta Robots'),
            'values' => $this->metaRobotsOptions->toOptionArray(),
        ]);

        $designFieldset = $form->addFieldset('design_fieldset', [
            'legend' => __('Design'),
            'class'  => 'fieldset-wide',
        ]);

        $designFieldset->addField('layout', 'select', [
            'name'   => 'layout',
            'label'  => __('Layout'),
            'title'  => __('Layout'),
            'values' => $this->_layoutOptions->toOptionArray(),
        ]);

        if (!$post->getId()) {
            $post->addData([
                'allow_comment'    => 1,
                'meta_title'       => $this->helperData->getMetaTitle(),
                'meta_description' => $this->helperData->getMetaDescription(),
                'meta_keywords'    => $this->helperData->getMetaKeywords(),
                'meta_robots'      => $this->helperData->getMetaRobots(),
            ]);
        }

        /** Get the public_date from database */
        if ($post->getData('publish_date')) {
            $publicDateTime = new \DateTime($post->getData('publish_date'), new DateTimeZone('UTC'));
            $publicDateTime->setTimezone(new DateTimeZone($this->_localeDate->getConfigTimezone()));
            $publicDateTime = $publicDateTime->format('m/d/Y H:i:s');
            $post->setData('publish_date', $publicDateTime);
        }

        $form->addValues($post->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Post');
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
}
