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

namespace Kemana\Promotion\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface PromotionInterface
 * @package Kemana\Promotion\Api\Data
 */
interface PromotionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const PROMOTION_ID = 'promotion_id';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const SORT_ORDER = 'sort_order';
    const LANDING_IMAGE = 'landing_image';
    const LANDING_IMAGE_MOBILE = 'landing_image_mobile';
    const SHORT_CONTENT = 'short_content';
    const CONTENT = 'content';
    const STORES = 'stores';
    const CREATED_AT = 'created_at';
    const UPDATE_AT = 'update_at';
    const IS_ACTIVE = 'is_active';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    /**#@-*/


    /**
     * Get ID - Row ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Promotion ID
     *
     * @return int|null
     */
    public function getPromotionId();

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get Identifier
     *
     * @return string|null
     */
    public function getIdentifier();

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Get Landing Image
     *
     * @return string|null
     */
    public function getLandingImage();

    /**
     * Get Landing Image Mobile
     *
     * @return string|null
     */
    public function getLandingImageMobile();

    /**
     * Get Short Content
     *
     * @return string|null
     */
    public function getShortContent();

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Get Stores
     *
     * @return string|null
     */
    public function getStores();

    /**
     * Get Created At
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get Updated At
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get Is Active
     *
     * @return int|null
     */
    public function getIsActive();

    /**
     * Get Meta Title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get Meta Keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get Meta Description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set Promotion ID
     *
     * @param int $promotionId
     * @return int|null
     */
    public function setPromotionId($promotionId);

    /**
     * Set Title
     *
     * @param string $title
     * @return string|null
     */
    public function setTitle($title);

    /**
     * Set Identifier
     *
     * @param string $identifier
     * @return string|null
     */
    public function setIdentifier($identifier);

    /**
     * Set Sort Order
     *
     * @param int $sortOrder
     * @return int|null
     */
    public function setSortOrder($sortOrder);

    /**
     * Set Landing Image
     *
     * @param string $landingImage
     * @return string|null
     */
    public function setLandingImage($landingImage);

    /**
     * Set Landing Image Mobile
     *
     * @param string $landingImageMobile
     * @return string|null
     */
    public function setLandingImageMobile($landingImageMobile);

    /**
     * Set Short Content
     *
     * @param string $shortContent
     * @return int|null
     */
    public function setShortContent($shortContent);

    /**
     * Set Content
     *
     * @param string $content
     * @return string|null
     */
    public function setContent($content);

    /**
     * Set Stores
     *
     * @param string $stores
     * @return string|null
     */
    public function setStores($stores);

    /**
     * Set Created At
     *
     * @param string $createdAt
     * @return string|null
     */
    public function setCreatedAt($createdAt);

    /**
     * Set Updated At
     *
     * @param string $updatedAt
     * @return string|null
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set Is Active
     *
     * @param int $isActive
     * @return int|null
     */
    public function setIsActive($isActive);

    /**
     * Set Meta Title
     *
     * @param string $metaTitle
     * @return string|null
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set Meta Keywords
     *
     * @param string $metaKeyWords
     * @return string|null
     */
    public function setMetaKeyWords($metaKeyWords);

    /**
     * Set Meta Description
     *
     * @param string $metaDescription
     * @return string|null
     */
    public function setMetaDescription($metaDescription);

}
