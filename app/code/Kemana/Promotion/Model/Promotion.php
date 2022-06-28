<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Model;

use Kemana\Promotion\Api\PromotionRepositoryInterface as PromotionRepositoryInterface;
use Kemana\Promotion\Model\PromotionFactory as PromotionFactory;
use Kemana\Promotion\Model\ResourceModel\PromotionFactory as PromotionResourceFactory;

/**
 * Class Promotion
 *
 * @package Kemana\Promotion\Model
 */
class Promotion extends \Magento\Framework\Model\AbstractModel implements \Kemana\Promotion\Api\Data\PromotionInterface, \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'kemana_promotion';

    /**
     * @var string
     */
    protected $_cacheTag = 'kemana_promotion';

    /**
     * @var string
     */
    protected $_eventPrefix = 'kemana_promotion';

    /**
     * @var \Kemana\Promotion\Model\PromotionFactory
     */
    protected $promotionFactory;

    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepositoryInterface;

    /**
     * @var PromotionResourceFactory
     */
    protected $promotionResourceFactory;

    /**
     * Resource model and primary key
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Kemana\Promotion\Model\ResourceModel\Promotion');
        $this->setIdFieldName('promotion_id');
    }

    /**
     * Promotion constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Kemana\Promotion\Model\PromotionFactory $promotionFactory
     * @param PromotionRepositoryInterface $promotionRepositoryInterface
     * @param PromotionResourceFactory $promotionResourceFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PromotionFactory $promotionFactory,
        PromotionRepositoryInterface $promotionRepositoryInterface,
        PromotionResourceFactory $promotionResourceFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->promotionFactory = $promotionFactory;
        $this->promotionRepositoryInterface = $promotionRepositoryInterface;
        $this->promotionResourceFactory = $promotionResourceFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(self::PROMOTION_ID);
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function getPromotionId()
    {
        return $this->_getData(self::PROMOTION_ID);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->_getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->_getData(self::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->_getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function getLandingImage()
    {
        return $this->_getData(self::LANDING_IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function getLandingImageMobile()
    {
        return $this->_getData(self::LANDING_IMAGE_MOBILE);
    }

    /**
     * @inheritDoc
     */
    public function getShortContent()
    {
        return $this->_getData(self::SHORT_CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->_getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function getStores()
    {
        return $this->_getData(self::STORES);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATE_AT);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->_getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        return $this->_getData(self::META_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords()
    {
        return $this->_getData(self::META_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->_getData(self::META_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setPromotionId($promotionId)
    {
        return $this->setData(self::PROMOTION_ID, $promotionId);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($identifier)
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritDoc
     */
    public function setLandingImage($landingImage)
    {
        return $this->setData(self::LANDING_IMAGE, $landingImage);
    }

    /**
     * @inheritDoc
     */
    public function setLandingImageMobile($landingImageMobile)
    {
        return $this->setData(self::LANDING_IMAGE_MOBILE, $landingImageMobile);
    }

    /**
     * @inheritDoc
     */
    public function setShortContent($shortContent)
    {
        return $this->setData(self::SHORT_CONTENT, $shortContent);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function setStores($stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATE_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritDoc
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setData(self::META_TITLE, $metaTitle);
    }

    /**
     * @inheritDoc
     */
    public function setMetaKeyWords($metaKeyWords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeyWords);
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }
}
