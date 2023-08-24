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

namespace Kemana\Blog\Model;

use Kemana\Blog\Helper\Data;
use Magento\Framework\Model\AbstractModel;

/**
 * Class PostLike
 * @package Kemana\Blog\Model
 */
class PostHistory extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'kemana_blog_post_history';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'kemana_blog_post_history';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'kemana_blog_post_history';

    /**
     * @var string
     */
    protected $_idFieldName = 'like_id';

    /**
     * @var \Kemana\Blog\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Kemana\Blog\Helper\Data $helperData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_construct();
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\PostHistory::class);
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param $postId
     *
     * @return int
     */
    public function getSumPostHistory($postId)
    {
        return $this->getCollection()->addFieldToFilter('post_id', $postId)->count();
    }

    /**
     * @param $postId
     */
    public function removeFirstHistory($postId)
    {
        $this->getCollection()->addFieldToFilter('post_id', $postId)->getFirstItem()->delete();
    }

    /**
     * @return array|mixed
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }
        $data = [];
        foreach ($this->helperData->jsonDecode($this->getProductIds()) as $key => $value) {
            $data[$key] = $value['position'];
        }

        return $data;
    }
}
