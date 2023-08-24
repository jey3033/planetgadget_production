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

namespace Kemana\Blog\Model\ResourceModel\Category;

use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Kemana\Blog\Api\Data\SearchResult\CategorySearchResultInterface;
use Kemana\Blog\Model\Category;
use Zend_Db_Select;

/**
 * Class Collection
 * @package Kemana\Blog\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection implements CategorySearchResultInterface
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'kemana_blog_category_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'category_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Category::class, \Kemana\Blog\Model\ResourceModel\Category::class);
    }

    /**
     * @param $field
     * @param null $condition
     *
     * @return $this
     */
    public function addAttributeToFilter($field, $condition = null)
    {
        return $this->addFieldToFilter($field, $condition);
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'entity_id') {
            $field = 'category_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setProductStoreId($storeId)
    {
        return $this;
    }

    /**
     * @param $count
     *
     * @return $this
     */
    public function setLoadProductCount($count)
    {
        return $this;
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this;
    }

    /**
     * @param $attribute
     * @param bool $joinType
     *
     * @return $this
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = 'category_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * add if filter
     *
     * @param $categoryIds
     *
     * @return $this
     */
    public function addIdFilter($categoryIds)
    {
        $condition = '';

        if (is_array($categoryIds)) {
            if (!empty($categoryIds)) {
                $condition = ['in' => $categoryIds];
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition) {
            $this->addFieldToFilter('category_id', $condition);
        }

        return $this;
    }
}
