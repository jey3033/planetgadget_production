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
 * Interface CommentInterface
 * @package Kemana\Blog\Api\Data
 */
interface CommentInterface
{
    /**
     * Constants used as data array keys
     */
    const COMMENT_ID    = 'comment_id';
    const POST_ID       = 'post_id';
    const CONTENT       = 'content';
    const STORE_IDS     = 'store_ids';
    const CREATED_AT    = 'created_at';
    const STATUS        = 'status';
    const IMPORT_SOURCE = 'import_source';
    const USER_NAME     = 'user_name';
    const USER_EMAIL    = 'user_email';

    const ATTRIBUTES = [
        self::COMMENT_ID,
        self::POST_ID,
        self::CONTENT,
        self::STORE_IDS,
        self::CREATED_AT,
        self::USER_EMAIL,
        self::USER_NAME,
        self::IMPORT_SOURCE
    ];

    /**
     * Get Comment id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set Comment Id
     *
     * @param int $commentId
     *
     * @return $this
     */
    public function setId($commentId);

    /**
     * Get Post id
     *
     * @return int|null
     */
    public function getPostId();

    /**
     * Set Post Id
     *
     * @param int $postId
     *
     * @return $this
     */
    public function setPostId($postId);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status Id
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Comment content
     *
     * @return string/null
     */
    public function getContent();

    /**
     * Set Content
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * Get Post Store Id
     *
     * @return int/null
     */
    public function getStoreIds();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setStoreIds($id);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * @return string|null
     */
    public function getImportSource();

    /**
     * @return string|null
     */
    public function getUserEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setUserEmail($email);

    /**
     * @return string|null
     */
    public function getUserName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setUserName($name);
}
