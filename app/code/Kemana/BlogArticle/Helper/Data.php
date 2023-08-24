<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_BlogArticle
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\BlogArticle\Helper;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use DateTimeZone;
use Exception;
use Kemana\Blog\Helper\AbstractData as CoreHelper;
use Kemana\Blog\Helper\Image as HelperImage;
use Kemana\Blog\Model\Author;
use Kemana\Blog\Model\AuthorFactory;
use Kemana\Blog\Model\Category;
use Kemana\Blog\Model\CategoryFactory;
use Kemana\Blog\Model\Config\Source\SideBarLR;
use Kemana\Blog\Model\Post;
use Kemana\Blog\Model\PostFactory;
use Kemana\Blog\Model\PostHistoryFactory;
use Kemana\Blog\Model\ResourceModel\Author\Collection as AuthorCollection;
use Kemana\Blog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Kemana\Blog\Model\ResourceModel\Post\Collection as PostCollection;
use Kemana\Blog\Model\ResourceModel\Tag\Collection as TagCollection;
use Kemana\Blog\Model\ResourceModel\Topic\Collection;
use Kemana\Blog\Model\Tag;
use Kemana\Blog\Model\TagFactory;
use Kemana\Blog\Model\Topic;
use Kemana\Blog\Model\TopicFactory;
use Magento\Backend\App\Config;
use Magento\Backend\App\ConfigInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filter\TranslitUrl;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'blog';
    const TYPE_POST          = 'post';
    const TYPE_CATEGORY      = 'category';
    const TYPE_TAG           = 'tag';
    const TYPE_TOPIC         = 'topic';
    const TYPE_HISTORY       = 'history';
    const TYPE_AUTHOR        = 'author';
    const TYPE_MONTHLY       = 'month';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var PostFactory
     */
    public $postFactory;

    /**
     * @var CategoryFactory
     */
    public $categoryFactory;

    /**
     * @var TagFactory
     */
    public $tagFactory;

    /**
     * @var TopicFactory
     */
    public $topicFactory;

    /**
     * @var AuthorFactory
     */
    public $authorFactory;

    /**
     * @var TranslitUrl
     */
    public $translitUrl;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var PostHistoryFactory
     */
    protected $postHistoryFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $_productMetadata;

    /**
     * @var HelperImage
     */
    protected $helperImage;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param JsonHelper $jsonHelper
     * @param State $state
     * @param ConfigInterface $config
     * @param ProductMetadataInterface $productMetadata
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param TopicFactory $topicFactory
     * @param AuthorFactory $authorFactory
     * @param PostHistoryFactory $postHistoryFactory
     * @param TranslitUrl $translitUrl
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param DateTime $dateTime
     * @param HelperImage $helperImage
     * @param Context $context
     * @param TimezoneInterface $s
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        JsonHelper $jsonHelper,
        State $state,
        ConfigInterface $config,
        ProductMetadataInterface $productMetadata,
        PostFactory $postFactory,
        CategoryFactory $categoryFactory,
        TagFactory $tagFactory,
        TopicFactory $topicFactory,
        AuthorFactory $authorFactory,
        PostHistoryFactory $postHistoryFactory,
        TranslitUrl $translitUrl,
        Session $customerSession,
        HttpContext $httpContext,
        DateTime $dateTime,
        HelperImage $helperImage,
        TimezoneInterface $timezoneInterface
    ) {
        $this->postFactory        = $postFactory;
        $this->categoryFactory    = $categoryFactory;
        $this->tagFactory         = $tagFactory;
        $this->topicFactory       = $topicFactory;
        $this->authorFactory      = $authorFactory;
        $this->postHistoryFactory = $postHistoryFactory;
        $this->translitUrl        = $translitUrl;
        $this->dateTime           = $dateTime;
        $this->customerSession    = $customerSession;
        $this->_httpContext       = $httpContext;
        $this->_productMetadata   = $productMetadata;
        $this->helperImage        = $helperImage;
        $this->_timezoneInterface = $timezoneInterface;

        parent::__construct($context, $storeManager, $jsonHelper, $state, $config, $productMetadata);
    }

    /**
     * @param $dateTime
     * @param $formatString
     * @return string
     */
    public function getTimeAccordingToTimeZone($dateTime, $formatString)
    {
        $today = $this->_timezoneInterface->date()->format($formatString);    
        $dateTimeAsTimeZone = $this->_timezoneInterface
                                    ->date(new \DateTime($dateTime))
                                    ->format($formatString);
        return $dateTimeAsTimeZone;
    }

    /**
     * @return bool
     */
    public function isEnabledReview()
    {
        $groupId = (string) $this->_httpContext->getValue(CustomerContext::CONTEXT_GROUP);

        if ($this->getConfigGeneral('is_review')
            && in_array($groupId, explode(',', $this->getConfigGeneral('review_mode')), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getReviewMode()
    {
        $login = $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);

        if (!$login
            && in_array('0', explode(',', $this->getConfigGeneral('review_mode')), true)
        ) {
            return '0';
        }

        return '1';
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->_productMetadata->getVersion();
    }

    /**
     * @return int|null
     */
    public function getCurrentUser()
    {
        return $this->customerSession->getId();
    }

    /**
     * @return int|null
     */
    public function getCustomerIdByContext()
    {
        return $this->_httpContext->getValue('mp_customer_id') ?: $this->customerSession->getId();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        return $this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        $collection = $this->getAuthorCollection();

        return empty($collection->getSize());
    }

    /**
     * @return mixed
     */
    public function isEnabledAuthor()
    {
        if (!$this->_httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return false;
        }

        return $this->getCurrentAuthor() ? true : false;
    }

    /**
     * Set Customer Id in Context
     */
    public function setCustomerContextId()
    {
        $customer = $this->customerSession->getCustomerData();
        if (!$this->_httpContext->getValue('mp_customer_id') && $customer) {
            $this->_httpContext->setValue('mp_customer_id', $customer->getId(), 0);
        }
    }

    /**
     * @return DataObject
     */
    public function getCurrentAuthor()
    {
        $collection = $this->getAuthorCollection();

        return $collection ? $collection->getFirstItem() : null;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthorCollection()
    {
        if ($customerId = $this->_httpContext->getValue('mp_customer_id')) {
            return $this->getFactoryByType('author')->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        }

        return null;
    }

    /**
     * @return Image
     */
    public function getImageHelper()
    {
        return $this->helperImage;
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBlogConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array|mixed|string
     */
    public function getSidebarLayout($storeId = null)
    {
        $sideBarConfig = $this->getConfigValue(self::CONFIG_MODULE_PATH . '/sidebar/sidebar_left_right', $storeId);
        if ($sideBarConfig == 0) {
            return SideBarLR::LEFT;
        }

        if ($sideBarConfig == 1) {
            return SideBarLR::RIGHT;
        }

        return $sideBarConfig;
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSeoConfig($code, $storeId = null)
    {
        return $this->getBlogConfig('seo/' . $code, $storeId);
    }

    /**
     * @return mixed
     */
    public function showAuthorInfo()
    {
        return $this->getConfigGeneral('display_author');
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getBlogName($store = null)
    {
        return $this->getConfigGeneral('name', $store) ?: __('Blog');
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getRoute($store = null)
    {
        return $this->getConfigGeneral('url_prefix', $store) ?: 'blog';
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getUrlSuffix($store = null)
    {
        return $this->getConfigGeneral('url_suffix', $store)
        ? '.' . $this->getConfigGeneral('url_suffix', $store) : '';
    }

    /**
     * Get current theme id
     * @return mixed
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue(DesignInterface::XML_PATH_THEME_ID);
    }

    /**
     * @param null $type
     * @param null $id
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostCollection($type = null, $id = null, $storeId = null)
    {
        if ($id === null) {
            $id = $this->_request->getParam('id');
        }

        /** @var PostCollection $collection */
        $collection = $this->getPostList($storeId);

        switch ($type) {
            case self::TYPE_AUTHOR:
                $collection->addFieldToFilter('author_id', $id);
                break;
            case self::TYPE_CATEGORY:
                $collection->join(
                    ['category' => $collection->getTable('kemana_blog_post_category')],
                    'main_table.post_id=category.post_id AND category.category_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_TAG:
                $collection->join(
                    ['tag' => $collection->getTable('kemana_blog_post_tag')],
                    'main_table.post_id=tag.post_id AND tag.tag_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_TOPIC:
                $collection->join(
                    ['topic' => $collection->getTable('kemana_blog_post_topic')],
                    'main_table.post_id=topic.post_id AND topic.topic_id=' . $id,
                    ['position']
                );
                break;
            case self::TYPE_MONTHLY:
                $collection->addFieldToFilter('publish_date', ['like' => $id . '%']);
                break;
        }

        return $collection;
    }

    /**
     * @param null $storeId
     *
     * @return PostCollection
     * @throws NoSuchEntityException
     */
    public function getPostList($storeId = null)
    {
        /** @var PostCollection $collection */
        $collection = $this->getObjectList(self::TYPE_POST, $storeId)
            ->addFieldToFilter('publish_date', ['to' => $this->dateTime->date()])
            ->setOrder('publish_date', 'desc');

        return $collection;
    }

    /**
     * @param $array
     *
     * @return \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
     */
    public function getCategoryCollection($array)
    {
        try {
            $collection = $this->getObjectList(self::TYPE_CATEGORY)
                ->addFieldToFilter('category_id', ['in' => $array]);

            return $collection;
        } catch (Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }

        return null;
    }

    /**
     * Get object collection (Category, Tag, Post, Topic)
     *
     * @param null $type
     * @param null $storeId
     *
     * @return AuthorCollection|CategoryCollection|PostCollection|TagCollection|Collection
     * @throws NoSuchEntityException
     */
    public function getObjectList($type = null, $storeId = null)
    {
        /** @var AuthorCollection|CategoryCollection|PostCollection|TagCollection|Collection $collection */
        $collection = $this->getFactoryByType($type)
            ->create()
            ->getCollection()
            ->addFieldToFilter('enabled', 1);

        $this->addStoreFilter($collection, $storeId);

        return $collection;
    }

    /**
     * @param $collection
     * @param null $storeId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $collection->addFieldToFilter('store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId],
        ]);

        return $collection;
    }

    /**
     * @param $post
     * @param bool $modify
     *
     * @return Author
     */
    public function getAuthorByPost($post, $modify = false)
    {
        $author = $this->authorFactory->create();

        $authorId = $modify ? $post->getModifierId() : $post->getAuthorId();
        if ($authorId) {
            $author->load($authorId);
        }

        return $author;
    }

    /**
     * @param null $urlKey
     * @param null $type
     * @param null $store
     *
     * @return string
     */
    public function getBlogUrl($urlKey = null, $type = null, $store = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url    = $this->getUrl($this->getRoute($store) . '/' . $urlKey);
        $url    = explode('?', $url);
        $url    = $url[0];

        return rtrim($url, '/') . $this->getUrlSuffix($store);
    }

    /**
     * @param $value
     * @param null $code
     * @param null $type
     *
     * @return Author|Category|Post|Tag|Topic
     */
    public function getObjectByParam($value, $code = null, $type = null)
    {
        $object = $this->getFactoryByType($type)
            ->create()
            ->load($value, $code);

        return $object;
    }

    /**
     * @param $type
     *
     * @return AuthorFactory|CategoryFactory|PostFactory|TagFactory|TopicFactory
     */
    public function getFactoryByType($type = null)
    {
        switch ($type) {
            case self::TYPE_CATEGORY:
                $object = $this->categoryFactory;
                break;
            case self::TYPE_TAG:
                $object = $this->tagFactory;
                break;
            case self::TYPE_AUTHOR:
                $object = $this->authorFactory;
                break;
            case self::TYPE_TOPIC:
                $object = $this->topicFactory;
                break;
            case self::TYPE_HISTORY:
                $object = $this->postHistoryFactory;
                break;
            default:
                $object = $this->postFactory;
        }

        return $object;
    }

    /**
     * Generate url_key for post, tag, topic, category, author
     *
     * @param $resource
     * @param $object
     * @param $name
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateUrlKey($resource, $object, $name)
    {
        $attempt = -1;
        do {
            if ($attempt++ >= 10) {
                throw new LocalizedException(__('Unable to generate url key. Please check the setting and try again.'));
            }

            $urlKey = $this->translitUrl->filter($name);
            if ($urlKey) {
                $urlKey .= ($attempt ?: '');
            }
        } while ($this->checkUrlKey($resource, $object, $urlKey));

        return $urlKey;
    }

    /**
     * @param $resource
     * @param $object
     * @param $urlKey
     *
     * @return bool
     */
    public function checkUrlKey($resource, $object, $urlKey)
    {
        if (empty($urlKey)) {
            return true;
        }

        $adapter = $resource->getConnection();
        $select  = $adapter->select()
            ->from($resource->getMainTable(), '*')
            ->where('url_key = :url_key');

        $binds = ['url_key' => (string) $urlKey];

        if ($id = $object->getId()) {
            $select->where($resource->getIdFieldName() . ' != :object_id');
            $binds['object_id'] = (int) $id;
        }

        return $adapter->fetchOne($select, $binds);
    }

    /**
     * get date formatted
     *
     * @param $date
     * @param bool $monthly
     *
     * @return false|string
     * @throws Exception
     */
    public function getDateFormat($date, $monthly = false)
    {
        $dateTime = new \DateTime($date, new DateTimeZone('UTC'));
        $dateTime->setTimezone(new DateTimeZone($this->getTimezone()));

        $dateType = $this->getBlogConfig($monthly ? 'monthly_archive/date_type_monthly' : 'general/date_type');

        return $dateTime->format($dateType);
    }

    /**
     * get configuration zone
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param $route
     * @param array $params
     *
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param $object
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    public function checkStore($object)
    {
        $storeEnable = explode(',', $object->getStoreIds());

        return in_array('0', $storeEnable, true)
        || in_array((string) $this->storeManager->getStore()->getId(), $storeEnable, true);
    }

    /**
     * get meta title
     * @return mixed
     */
    public function getMetaTitle()
    {
        return $this->getConfigValue('blog/seo/meta_title');
    }

    /**
     * get meta description
     * @return mixed
     */
    public function getMetaDescription()
    {
        return $this->getConfigValue('blog/seo/meta_description');
    }

    /**
     * get meta keywords
     * @return mixed
     */
    public function getMetaKeywords()
    {
        return $this->getConfigValue('blog/seo/meta_keywords');
    }

    /**
     * get meta robots
     * @return mixed
     */
    public function getMetaRobots()
    {
        return $this->getConfigValue('blog/seo/meta_robots');
    }

    /**
     * get cms wysiwyg editor
     * @return mixed
     */
    public function getCmsWysiwygEditor()
    {
        return $this->getConfigValue('cms/wysiwyg/editor');
    }

    /**
     * get catalog seo title separator
     * @return mixed
     */
    public function getCatalogSeoTitleSeparator()
    {
        return $this->getConfigValue('catalog/seo/title_separator');
    }

    /**
     * get design theme id
     * @return mixed
     */
    public function getDesignThemeId()
    {
        return $this->getConfigValue(DesignInterface::XML_PATH_THEME_ID);
    }

    /**
     * get localte code
     * @return mixed
     */
    public function getLocaleCode($storeModel)
    {
        return $this->getConfigValue('general/locale/code', $storeModel);
    }
}
