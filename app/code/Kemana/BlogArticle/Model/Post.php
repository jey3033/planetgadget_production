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

namespace Kemana\BlogArticle\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Kemana\Blog\Model\ResourceModel\Post as PostResource;
use Kemana\Blog\Model\ResourceModel\Post\Collection;
use Kemana\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Kemana\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Kemana\Blog\Model\ResourceModel\Topic\CollectionFactory as TopicCollectionFactory;

/**
 * @method Post setName($name)
 * @method Post setShortDescription($shortDescription)
 * @method Post setPostContent($postContent)
 * @method Post setImage($image)
 * @method Post setViews($views)
 * @method Post setEnabled($enabled)
 * @method Post setUrlKey($urlKey)
 * @method Post setInRss($inRss)
 * @method Post setAllowComment($allowComment)
 * @method Post setMetaTitle($metaTitle)
 * @method Post setMetaDescription($metaDescription)
 * @method Post setMetaKeywords($metaKeywords)
 * @method Post setMetaRobots($metaRobots)
 * @method mixed getName()
 * @method mixed getPostContent()
 * @method mixed getImage()
 * @method mixed getViews()
 * @method mixed getEnabled()
 * @method mixed getUrlKey()
 * @method mixed getInRss()
 * @method mixed getAllowComment()
 * @method mixed getMetaTitle()
 * @method mixed getMetaDescription()
 * @method mixed getMetaKeywords()
 * @method mixed getMetaRobots()
 * @method Post setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Post setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Post setTagsData(array $data)
 * @method Post setTopicsData(array $data)
 * @method Post setProductsData(array $data)
 * @method array getTagsData()
 * @method array getProductsData()
 * @method array getTopicsData()
 * @method Post setIsChangedTagList(bool $flag)
 * @method Post setIsChangedProductList(bool $flag)
 * @method Post setIsChangedTopicList(bool $flag)
 * @method Post setIsChangedCategoryList(bool $flag)
 * @method bool getIsChangedTagList()
 * @method bool getIsChangedTopicList()
 * @method bool getIsChangedCategoryList()
 * @method Post setAffectedTagIds(array $ids)
 * @method Post setAffectedEntityIds(array $ids)
 * @method Post setAffectedTopicIds(array $ids)
 * @method Post setAffectedCategoryIds(array $ids)
 * @method bool getAffectedTagIds()
 * @method bool getAffectedTopicIds()
 * @method bool getAffectedCategoryIds()
 * @method array getCategoriesIds()
 * @method Post setCategoriesIds(array $categoryIds)
 * @method array getTagsIds()
 * @method Post setTagsIds(array $tagIds)
 * @method array getTopicsIds()
 * @method Post setTopicsIds(array $topicIds)
 */
class Post extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'kemana_blog_post';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'kemana_blog_post';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'kemana_blog_post';

    /**
     * Tag Collection
     *
     * @var ResourceModel\Tag\Collection
     */
    public $tagCollection;

    /**
     * Topic Collection
     *
     * @var ResourceModel\Topic\Collection
     */
    public $topicCollection;

    /**
     * Blog Category Collection
     *
     * @var ResourceModel\Category\Collection
     */
    public $categoryCollection;

    /**
     * Tag Collection Factory
     *
     * @var CollectionFactory
     */
    public $tagCollectionFactory;

    /**
     * Topic Collection Factory
     *
     * @var TopicCollectionFactory
     */
    public $topicCollectionFactory;

    /**
     * Blog Category Collection Factory
     *
     * @var CategoryCollectionFactory
     */
    public $categoryCollectionFactory;

    /**
     * Post Collection Factory
     *
     * @var PostCollectionFactory
     */
    public $postCollectionFactory;

    /**
     * Related Post Collection
     *
     * @var Collection
     */
    public $relatedPostCollection;

    /**
     * Previous Post Collection
     *
     * @var Collection
     */
    public $prevPostCollection;

    /**
     * Next Post Collection
     *
     * @var Collection
     */
    public $nextPostCollection;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var ProductCollectionFactory
     */
    public $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public $productCollection;

    /**
     * @var TrafficFactory
     */
    protected $trafficFactory;

    /**
     * Post constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param Data $helperData
     * @param TrafficFactory $trafficFactory
     * @param CollectionFactory $tagCollectionFactory
     * @param TopicCollectionFactory $topicCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        Data $helperData,
        TrafficFactory $trafficFactory,
        CollectionFactory $tagCollectionFactory,
        TopicCollectionFactory $topicCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        PostCollectionFactory $postCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->dateTime = $dateTime;
        $this->trafficFactory = $trafficFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }

    /**
     * @inheritdoc
     */
    public function afterSave()
    {
        if ($this->isObjectNew()) {
            $trafficModel = $this->trafficFactory->create()
                ->load($this->getId(), 'post_id');
            if (!$trafficModel->getId()) {
                $trafficModel->setData([
                    'post_id' => $this->getId(),
                    'numbers_view' => 0
                ])->save();
            }
        }

        return parent::afterSave();
    }

    /**
     * @param bool $shorten
     *
     * @return mixed|string
     */
    public function getShortDescription($shorten = false)
    {
        $shortDescription = $this->getData('short_description');

        $maxLength = 200;
        if ($shorten && strlen($shortDescription) > $maxLength) {
            $shortDescription = substr($shortDescription, 0, $maxLength) . '...';
        }

        return $shortDescription;
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getUrl($store = null)
    {
        return $this->helperData->getBlogUrl($this, Data::TYPE_POST, $store);
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
        $values['in_rss'] = '1';
        $values['enabled'] = '1';
        $values['allow_comment'] = '1';
        $values['store_ids'] = '1';

        return $values;
    }

    /**
     * @return ResourceModel\Tag\Collection
     */
    public function getSelectedTagsCollection()
    {
        if ($this->tagCollection === null) {
            $collection = $this->tagCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('kemana_blog_post_tag'),
                'main_table.tag_id=' . $this->getResource()->getTable('kemana_blog_post_tag') . '.tag_id AND '
                . $this->getResource()->getTable('kemana_blog_post_tag') . '.post_id=' . $this->getId(),
                ['position']
            )->where("main_table.enabled='1'");
            $this->tagCollection = $collection;
        }

        return $this->tagCollection;
    }

    /**
     * @return ResourceModel\Topic\Collection
     */
    public function getSelectedTopicsCollection()
    {
        $collection = $this->topicCollectionFactory->create();
        $collection->join(
            'kemana_blog_post_topic',
            'main_table.topic_id=kemana_blog_post_topic.topic_id AND kemana_blog_post_topic.post_id=' . $this->getId(),
            ['position']
        );
        $this->topicCollection = $collection;

        return $this->topicCollection;
    }

    /**
     * @return ResourceModel\Category\Collection
     */
    public function getSelectedCategoriesCollection()
    {
        if ($this->categoryCollection === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->join(
                $this->getResource()->getTable('kemana_blog_post_category'),
                'main_table.category_id=' . $this->getResource()->getTable('kemana_blog_post_category') .
                '.category_id AND ' . $this->getResource()->getTable('kemana_blog_post_category') . '.post_id="'
                . $this->getId() . '"',
                ['position']
            );
            $this->categoryCollection = $collection;
        }

        return $this->categoryCollection;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryIds()
    {
        if (!$this->hasData('category_ids')) {
            $ids = $this->_getResource()->getCategoryIds($this);
            $this->setData('category_ids', $ids);
        }

        return (array)$this->_getData('category_ids');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTagIds()
    {
        if (!$this->hasData('tag_ids')) {
            $ids = $this->_getResource()->getTagIds($this);

            $this->setData('tag_ids', $ids);
        }

        return (array)$this->_getData('tag_ids');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getTopicIds()
    {
        if (!$this->hasData('topic_ids')) {
            $ids = $this->_getResource()->getTopicIds($this);

            $this->setData('topic_ids', $ids);
        }

        return (array)$this->_getData('topic_ids');
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getViewTraffic()
    {
        if (!$this->hasData('view_traffic')) {
            $traffic = $this->_getResource()->getViewTraffic($this);

            $this->setData('view_traffic', $traffic[0]);
        }

        return $this->_getData('view_traffic');
    }

    /**
     * @param null $limit
     *
     * @return ResourceModel\Post\Collection|null
     * @throws LocalizedException
     */
    public function getRelatedPostsCollection($limit = null)
    {
        $topicIds = $this->_getResource()->getTopicIds($this);
        if (count($topicIds)) {
            $collection = $this->postCollectionFactory->create();
            $collection->getSelect()
                ->join(
                    ['topic' => $this->getResource()->getTable('kemana_blog_post_topic')],
                    'main_table.post_id=topic.post_id AND topic.post_id != "' . $this->getId()
                    . '" AND topic.topic_id IN (' . implode(',', $topicIds) . ')',
                    ['position']
                )->group('main_table.post_id');

            if ($limit = (int)$this->helperData->getBlogConfig('general/related_post')) {
                $collection->getSelect()
                    ->limit($limit);
            }
            $collection->addFieldToFilter('enabled', '1');

            return $collection;
        }

        return null;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getSelectedProductsCollection()
    {
        if ($this->productCollection === null) {
            $collection = $this->productCollectionFactory->create();
            $collection->getSelect()->join(
                $this->getResource()->getTable('kemana_blog_post_product'),
                'e.entity_id=' . $this->getResource()->getTable('kemana_blog_post_product')
                . '.entity_id AND ' . $this->getResource()->getTable('kemana_blog_post_product') . '.post_id='
                . $this->getId(),
                ['position']
            );
            $this->productCollection = $collection;
        }

        return $this->productCollection;
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $array = $this->getData('products_position');
        if ($array === null) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }

        return $array;
    }

    /**
     * get previous post
     * @return Collection
     */
    public function getPrevPost()
    {
        if ($this->prevPostCollection === null) {
            $collection = $this->postCollectionFactory->create();
            $collection->addFieldToFilter('post_id', ['lt' => $this->getId()])
                ->setOrder('post_id', 'DESC')->setPageSize(1)->setCurPage(1);
            $this->prevPostCollection = $collection;
        }

        return $this->prevPostCollection;
    }

    /**
     * get next post
     * @return Collection
     */
    public function getNextPost()
    {
        if ($this->nextPostCollection === null) {
            $collection = $this->postCollectionFactory->create();
            $collection->addFieldToFilter('post_id', ['gt' => $this->getId()])
                ->setOrder('post_id', 'ASC')->setPageSize(1)->setCurPage(1);
            $this->nextPostCollection = $collection;
        }

        return $this->nextPostCollection;
    }
}
