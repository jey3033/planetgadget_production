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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Kemana\Blog\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Kemana\Blog\Model\ResourceModel\Post\CollectionFactory;

/**
 * Class Comment
 * @package Kemana\Blog\Model
 */
class Comment extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'kemana_blog_comment';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'kemana_blog_comment';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'kemana_blog_comment';

    /**
     * @var string
     */
    protected $_idFieldName = 'comment_id';

    /**
     * Post Collection Factory
     *
     * @var CollectionFactory
     */
    public $postCollectionFactory;

    /**
     * @var CommentCollectionFactory
     */
    public $commentCollectionFactory;

    /**
     * Comment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $postCollectionFactory
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $postCollectionFactory,
        CommentCollectionFactory $commentCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->postCollectionFactory = $postCollectionFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Comment::class);
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
