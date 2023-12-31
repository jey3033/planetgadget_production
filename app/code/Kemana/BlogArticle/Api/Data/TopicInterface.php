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

namespace Kemana\Blog\Api\Data;

/**
 * Interface TopicInterface
 * @package Kemana\Blog\Api\Data
 */
interface TopicInterface
{
    /**
     * Constants used as data array keys
     */
    const TOPIC_ID         = 'topic_id';
    const NAME             = 'name';
    const DESCRIPTION      = 'description';
    const STORE_IDS        = 'store_ids';
    const URL_KEY          = 'url_key';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS    = 'meta_keywords';
    const META_ROBOTS      = 'meta_robots';
    const UPDATED_AT       = 'updated_at';
    const CREATED_AT       = 'created_at';
    const IMPORT_SOURCE    = 'import_source';

    const ATTRIBUTES = [
        self::TOPIC_ID,
        self::NAME,
        self::DESCRIPTION,
        self::STORE_IDS,
        self::URL_KEY,
        self::META_TITLE,
        self::META_DESCRIPTION,
        self::META_KEYWORDS,
        self::META_ROBOTS,
        self::IMPORT_SOURCE
    ];

    /**
     * Get Post id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Post id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get Post Name
     *
     * @return string/null
     */
    public function getName();

    /**
     * Set Post Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * Get Post Description
     *
     * @return string/null
     */
    public function getDescription();

    /**
     * Set Post Short Description
     *
     * @param string $content
     *
     * @return $this
     */
    public function setDescription($content);

    /**
     * Get Post Store Id
     *
     * @return int/null
     */
    public function getStoreIds();

    /**
     * Set Post Store Id
     *
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreIds($storeId);

    /**
     * Get Post Image
     *
     * @return string/null
     */
    public function getImage();

    /**
     * Set Post Image
     *
     * @param string $content
     *
     * @return $this
     */
    public function setImage($content);

    /**
     * Get Post Url Key
     *
     * @return string/null
     */
    public function getUrlKey();

    /**
     * Set Post Url Key
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * Get Post Meta Title
     *
     * @return string/null
     */
    public function getMetaTitle();

    /**
     * Set Post Meta Title
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaTitle($meta);

    /**
     * Get Post Meta Description
     *
     * @return string/null
     */
    public function getMetaDescription();

    /**
     * Set Post Meta Description
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaDescription($meta);

    /**
     * Get Post Meta Keywords
     *
     * @return string/null
     */
    public function getMetaKeywords();

    /**
     * Set Post Meta Keywords
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaKeywords($meta);

    /**
     * Get Post Meta Robots
     *
     * @return string/null
     */
    public function getMetaRobots();

    /**
     * Set Post Meta Robots
     *
     * @param string $meta
     *
     * @return $this
     */
    public function setMetaRobots($meta);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get Post updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set Post updated date
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string|null
     */
    public function getImportSource();

    /**
     * @param string $importSource
     *
     * @return $this
     */
    public function setImportSource($importSource);
}
