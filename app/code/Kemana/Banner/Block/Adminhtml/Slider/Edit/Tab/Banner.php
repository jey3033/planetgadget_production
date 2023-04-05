<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Slider\Edit\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Helper\Data as backendHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render\GridImage;
use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render\Status;
use Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render\Type;
use Kemana\Banner\Model\BannerFactory;
use Kemana\Banner\Model\ResourceModel\Banner\Collection;
use Kemana\Banner\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;

/**
 * Class Banner
 * @package Kemana\Banner\Block\Adminhtml\Slider\Edit\Tab
 */
class Banner extends Extended implements TabInterface
{
    /**
     * Banner collection factory
     *
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Banner factory
     *
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * Banner constructor.
     *
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param Registry $coreRegistry
     * @param BannerFactory $bannerFactory
     * @param Context $context
     * @param backendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        BannerCollectionFactory $bannerCollectionFactory,
        Registry $coreRegistry,
        BannerFactory $bannerFactory,
        Context $context,
        backendHelper $backendHelper,
        array $data = []
    ) {
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->bannerFactory = $bannerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('banner_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getSlider()->getId()) {
            $this->setDefaultFilter(['in_banners' => 1]);
        }
    }

    /**
     * @return Extended|void
     */
    protected function _prepareCollection()
    {
        /** @var Collection $collection */
        $collection = $this->bannerCollectionFactory->create();
        if ($this->getSlider()->getId()) {
            $constraint = 'related.slider_id=' . $this->getSlider()->getId();
        } else {
            $constraint = 'related.slider_id=0';
        }
        $collection->getSelect()->joinLeft(
            ['related' => $collection->getTable('kemana_bannerslider_banner_slider')],
            'related.banner_id=main_table.banner_id AND ' . $constraint,
            ['position']
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this|Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banners', [
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_banner',
            'values'           => $this->_getSelectedBanners(),
            'align'            => 'center',
            'index'            => 'banner_id'
        ]);
        $this->addColumn('banner_id', [
            'header'           => __('ID'),
            'sortable'         => true,
            'index'            => 'banner_id',
            'type'             => 'number',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
        ]);

        $this->addColumn('image', [
            'header'           => __('Image'),
            'index'            => 'image',
            'header_css_class' => 'col-image',
            'column_css_class' => 'col-image',
            'sortable'         => false,
            'renderer'         => GridImage::class
        ]);

        $this->addColumn('name', [
            'header'           => __('Name'),
            'index'            => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
        ]);

        $this->addColumn('type', [
            'header'           => __('Type'),
            'index'            => 'type',
            'header_css_class' => 'col-type',
            'column_css_class' => 'col-type',
            'renderer'         => Type::class
        ]);

        $this->addColumn('status', [
            'header'           => __('Status'),
            'index'            => 'status',
            'header_css_class' => 'col-status',
            'column_css_class' => 'col-status',
            'renderer'         => Status::class
        ]);

        $this->addColumn('position', [
            'header'         => __('Position'),
            'name'           => 'position',
            'type'           => 'number',
            'validate_class' => 'validate-number',
            'index'          => 'position',
            'editable'       => true,
        ]);

        return $this;
    }

    /**
     * Retrieve selected Banners
     * @return array
     */
    protected function _getSelectedBanners()
    {
        $banners = $this->getSliderBanners();
        if (!is_array($banners)) {
            $banners = $this->getSlider()->getBannersPosition();

            return array_keys($banners);
        }

        return $banners;
    }

    /**
     * Retrieve selected Banners
     * @return array
     */
    public function getSelectedBanners()
    {
        $selected = $this->getSlider()->getBannersPosition();
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
     * @param \Kemana\Banner\Model\Banner|Object $item
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return '';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/bannersGrid',
            [
                'slider_id' => $this->getSlider()->getId()
            ]
        );
    }

    /**
     * @return \Kemana\Banner\Model\Slider
     */
    public function getSlider()
    {
        return $this->coreRegistry->registry('bannerslider_slider');
    }

    /**
     * @param Column $column
     *
     * @return $this|Extended
     * @throws LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_banners') {
            $bannerIds = $this->_getSelectedBanners();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', ['in' => $bannerIds]);
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addFieldToFilter('main_table.banner_id', ['nin' => $bannerIds]);
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
        return __('Banners');
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
        return $this->getUrl('bannerslider/slider/banners', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
