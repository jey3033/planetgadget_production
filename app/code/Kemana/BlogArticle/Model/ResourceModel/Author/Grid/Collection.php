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

namespace Kemana\Blog\Model\ResourceModel\Author\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Kemana\Blog\Model\ResourceModel\Author\Grid
 */
class Collection extends SearchResult
{
    /**
     * @return $this|SearchResult|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['cus' => $this->getTable('customer_entity')],
            'main_table.customer_id = cus.entity_id',
            ['email']
        )->joinLeft(
            ['post' => $this->getTable('kemana_blog_post')],
            'main_table.user_id = post.author_id',
            ['qty_post' => 'COUNT(post_id)']
        )->group('main_table.user_id');

        $this->addFilterToMap('name', 'main_table.name');
        $this->addFilterToMap('url_key', 'main_table.url_key');

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'qty_post') {
            foreach ($condition as $key => $value) {
                if ($key === 'like') {
                    $this->getSelect()->having('COUNT(post_id) LIKE ?', $value);
                }
            }
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
