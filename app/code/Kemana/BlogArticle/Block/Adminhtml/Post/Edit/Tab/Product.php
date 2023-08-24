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
use Kemana\Blog\Model\Tag;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Class Product
 * @package Kemana\Blog\Block\Adminhtml\Post\Edit\Tab
 */
class Product extends Extended implements TabInterface
{
    /**
     * @var CollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var ProductVisibility
     */
    protected $productVisibility;

    /**
     * Product constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Data $backendHelper
     * @param RequestInterface $request
     * @param CollectionFactory $productCollectionFactory
     * @param ProductStatus $productStatus
     * @param ProductVisibility $productVisibility
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Data $backendHelper,
        RequestInterface $request,
        CollectionFactory $productCollectionFactory,
        ProductStatus $productStatus,
        ProductVisibility $productVisibility,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry             = $coreRegistry;
        $this->request                  = $request;
        $this->productStatus            = $productStatus;
        $this->productVisibility        = $productVisibility;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('product_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);

        if ($this->getPost()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        $collection->clear();

        $collection->getSelect()->joinLeft(
            ['mp_p' => $collection->getTable('kemana_blog_post_product')],
            'e.entity_id = mp_p.entity_id',
            ['position', 'post_id']
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_products', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_product',
            'values'           => $this->_getSelectedProducts(),
            'align'            => 'center',
            'index'            => 'entity_id',
        ]);
        $this->addColumn('entity_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'entity_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
        ]);
        $this->addColumn('title', [
            'header'           => __('Sku'),
            'index'            => 'sku',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name',
        ]);

        if ($this->request->getParam('post_id') || $this->getPost()->getId()) {
            $this->addColumn('position', [
                'header'         => __('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
                'sortable'       => false,
                'edit_only'      => true,
            ]);
        } else {
            $this->addColumn('position', [
                'header'         => __('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
                'sortable'       => false,
                'filter'         => false,
                'edit_only'      => true,
            ]);
        }

        return $this;
    }

    /**
     * Retrieve selected Tags
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('post_products', null);
        if (!is_array($products)) {
            $products = $this->getPost()->getProductsPosition();

            return array_keys($products);
        }

        return $products;
    }

    /**
     * Retrieve selected Tags
     * @return array
     */
    public function getSelectedProducts()
    {
        $selected = $this->getPost()->getProductsPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }

        return $selected;
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
        return $this->getUrl('*/*/productsGrid', ['post_id' => $this->getPost()->getId()]);
    }

    /**
     * @return \Kemana\Blog\Model\Post
     */
    public function getPost()
    {
        return $this->coreRegistry->registry('kemana_blog_post');
    }

    /**
     * @param Column $column
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Products');
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
        return $this->getUrl('kemana_blog/post/products', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
