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
 * Interface AuthorInterface
 * @package Kemana\Blog\Api\Data
 */
interface AuthorInterface
{
    /**
     * Constants used as data array keys
     */
    const AUTHOR_ID         = 'user_id';
    const NAME              = 'name';
    const URL_KEY           = 'url_key';
    const SHORT_DESCRIPTION = 'short_description';
    const IMAGE             = 'image';
    const CUSTOMER_ID       = 'customer_id';
    const TYPE              = 'type';
    const STATUS            = 'status';
    const UPDATED_AT        = 'updated_at';
    const CREATED_AT        = 'created_at';
    const FACEBOOK_LINK     = 'facebook_link';
    const TWITTER_LINK      = 'twitter_link';
    const INSTAGRAM_LINK    = 'instagram_link';
    const LINKEDIN_LINK     = 'linkedin_link';

    const ATTRIBUTES = [
        self::AUTHOR_ID,
        self::NAME,
        self::URL_KEY,
        self::SHORT_DESCRIPTION,
        self::IMAGE,
        self::CUSTOMER_ID,
        self::TYPE,
        self::STATUS,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::FACEBOOK_LINK,
        self::TWITTER_LINK,
        self::INSTAGRAM_LINK,
        self::LINKEDIN_LINK
    ];

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return string/null
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string/null
     */
    public function getShortDescription();

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setShortDescription($content);

    /**
     * @return string/null
     */
    public function getImage();

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setImage($content);

    /**
     * @return string/null
     */
    public function getUrlKey();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrlKey($url);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param $createdAt
     *
     * @return mixed
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setCustomerIdId($id);

    /**
     * @return int|null
     */
    public function getType();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setType($id);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setStatus($id);

    /**
     * @return string|null
     */
    public function getFacebookLink();

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setFacebookLink($link);

    /**
     * @return string|null
     */
    public function getTwitterLink();

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setTwitterLink($link);

    /**
     * @return string|null
     */
    public function getInstagramLink();

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setInstagramLink($link);

    /**
     * @return string|null
     */
    public function getLinkedinLink();

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLinkedinLink($link);
}
