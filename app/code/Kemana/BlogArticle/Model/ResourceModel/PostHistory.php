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

use Kemana\Blog\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class PostLike
 * @package Kemana\Blog\Model\ResourceModel
 */
class PostHistory extends AbstractDb
{
    /**
     * @var \Kemana\Blog\Helper\Data
     */
    protected $helperData;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Kemana\Blog\Helper\Data $helperData
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Kemana\Blog\Helper\Data $helperData,
        $connectionName = null
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kemana_blog_post_history', 'history_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getData('store_ids'))) {
            $object->setData('store_ids', implode(',', $object->getData('store_ids')));
        }
        if (is_array($object->getData('categories_ids'))) {
            $object->setData('category_ids', implode(',', $object->getData('categories_ids')));
        }
        if (is_array($object->getData('topics_ids'))) {
            $object->setData('topic_ids', implode(',', $object->getData('topics_ids')));
        }
        if (is_array($object->getData('tags_ids'))) {
            $object->setData('tag_ids', implode(',', $object->getData('tags_ids')));
        }
        if (is_array($object->getData('products_data'))) {
            $data = $object->getData('products_data');
            foreach ($data as $key => $datum) {
                $data[$key]['position'] = $datum['position'] ?: '0';
            }
            $object->setData('product_ids', $this->helperData->jsonEncode($data));
        }

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        $object->setData('categories_ids', explode(',', $object->getCategoryIds()));
        $object->setData('tags_ids', explode(',', $object->getTagIds()));
        $object->setData('topics_ids', explode(',', $object->getTopicIds()));

        return parent::_afterLoad($object);
    }
}
