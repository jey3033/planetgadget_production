<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Kemana\Banner\Model\ResourceModel\Banner\Collection;
use Kemana\Banner\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class Slider
 * @package Kemana\Banner\Model
 */
class Slider extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'kemana_bannerslider_slider';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $cacheTag = 'kemana_bannerslider_slider';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $eventPrefix = 'kemana_bannerslider_slider';

    /**
     * Banner Collection
     *
     * @var \Kemana\Banner\Model\ResourceModel\Banner\Collection
     */
    protected $bannerCollection;

    /**
     * Banner Collection Factory
     *
     * @var \Kemana\Banner\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * Slider constructor.
     * @param CollectionFactory $bannerCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Kemana\Banner\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Function _construct
     */
    protected function _construct()
    {
        $this->_init('Kemana\Banner\Model\ResourceModel\Slider');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        $values['status'] = '1';

        return $values;
    }

    /**
     * @return array|mixed
     */
    public function getBannersPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('banners_position');
        if ($array === null) {
            $array = $this->getResource()->getBannersPosition($this);
            $this->setData('banners_position', $array);
        }

        return $array;
    }

    /**
     * @return \Kemana\Banner\Model\ResourceModel\Banner\Collection
     */
    public function getSelectedBannersCollection()
    {
        if ($this->bannerCollection === null) {
            $collection = $this->bannerCollectionFactory->create();
            $collection->getSelect()->join(
                ['banner_slider' => $this->getResource()->getTable('kemana_bannerslider_banner_slider')],
                'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $this->getId(),
                ['position']
            );
            $this->bannerCollection = $collection;
        }

        return $this->bannerCollection;
    }

}
