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

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Action;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Author;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Categories;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Store;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Tags;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\History\Topics;
use Kemana\Blog\Model\ResourceModel\PostHistory\Collection;
use Kemana\Blog\Model\Tag;

/**
 * Class Product
 * @package Kemana\Blog\Block\Adminhtml\Post\Edit\Tab
 */
class History extends Extended implements TabInterface
{

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var Collection
     */
    protected $historyCollection;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param Collection $historyCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        Collection $historyCollection,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->historyCollection = $historyCollection;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('history_id');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->historyCollection;
        $id = $this->getRequest()->getParam('id', 0);
        $collection->addFieldToFilter('post_id', $id);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'history_id',
            'type' => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);
        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);
        $this->addColumn('short_description', [
            'header' => __('Short Description'),
            'index' => 'short_description',
            'header_css_class' => 'col-short-description',
            'column_css_class' => 'col-short-description'
        ]);
        $this->addColumn('store_ids', [
            'header' => __('Store View'),
            'index' => 'store_ids',
            'renderer' => Store::class,
            'filter' => false,
            'header_css_class' => 'col-store-ids',
            'column_css_class' => 'col-store-ids'
        ]);
        $this->addColumn('category_ids', [
            'header' => __('Categories'),
            'index' => 'category_ids',
            'filter' => false,
            'renderer' => Categories::class,
            'header_css_class' => 'col-category-ids',
            'column_css_class' => 'col-category-ids'
        ]);
        $this->addColumn('topic_ids', [
            'header' => __('Topics'),
            'index' => 'topic_ids',
            'filter' => false,
            'renderer' => Topics::class,
            'header_css_class' => 'col-topic-ids',
            'column_css_class' => 'col-topic-ids'
        ]);
        $this->addColumn('tag_ids', [
            'header' => __('Tags'),
            'index' => 'tag_ids',
            'filter' => false,
            'renderer' => Tags::class,
            'header_css_class' => 'col-tag-ids',
            'column_css_class' => 'col-tag-ids'
        ]);
        $this->addColumn('modifier_id', [
            'header' => __('Modified by'),
            'index' => 'modifier_id',
            'renderer' => Author::class,
            'filter' => false,
            'sortable' => false,
            'header_css_class' => 'col-modifier-id',
            'column_css_class' => 'col-modifier-id'
        ]);
        $this->addColumn('updated_at', [
            'header' => __('Modified At'),
            'index' => 'updated_at',
            'filter' => false,
            'header_css_class' => 'col-updated-at',
            'column_css_class' => 'col-updated-at'
        ]);
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'template_id',
                'sortable' => false,
                'filter' => false,
                'no_link' => true,
                'renderer' => Action::class,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ]
        );

        return $this;
    }

    /**
     * @param Tag|Object $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', ['id' => $this->getPost()->getId()]);
    }

    /**
     * @return \Kemana\Blog\Model\Post
     */
    public function getPost()
    {
        return $this->coreRegistry->registry('kemana_blog_post');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Edit History');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('kemana_blog/post/history', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
