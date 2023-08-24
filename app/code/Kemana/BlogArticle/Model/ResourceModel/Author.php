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

namespace Kemana\Blog\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kemana\Blog\Helper\Data;

/**
 * Class Author
 * @package Kemana\Blog\Model\ResourceModel
 */
class Author extends AbstractDb
{
    /**
     * @var Data
     */
    public $helperData;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var string
     */
    public $postTable;

    /**
     * Author constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Data $helperData,
        DateTime $dateTime
    ) {
        $this->helperData = $helperData;
        $this->dateTime = $dateTime;

        parent::__construct($context);
        $this->postTable = $this->getTable('kemana_blog_post');
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('kemana_blog_author', 'user_id');
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUrlKey(
            $this->helperData->generateUrlKey($this, $object, $object->getUrlKey() ?: $object->getName())
        );

        if (!$object->isObjectNew()) {
            $timeStamp = $this->dateTime->gmtDate();
            $object->setUpdatedAt($timeStamp);
        }

        return $this;
    }

    /**
     * @param \Kemana\Blog\Model\Author $author
     *
     * @return array
     */
    public function getPostIds(\Kemana\Blog\Model\Author $author)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->postTable,
            'post_id'
        )
            ->where(
                'author_id = ?',
                (int)$author->getId()
            );

        return $adapter->fetchCol($select);
    }
}
