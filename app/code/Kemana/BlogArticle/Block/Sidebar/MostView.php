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

namespace Kemana\Blog\Block\Sidebar;

use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class MostView
 * @package Kemana\Blog\Block\Sidebar
 */
class MostView extends Frontend
{
    /**
     * @return Collection
     */
    public function getMostViewPosts()
    {
        $collection = $this->helperData->getPostList();
        $collection->getSelect()
            ->joinLeft(
                ['traffic' => $collection->getTable('kemana_blog_post_traffic')],
                'main_table.post_id=traffic.post_id',
                'numbers_view'
            )
            ->order('numbers_view DESC')
            ->limit((int)$this->helperData->getBlogConfig('sidebar/number_mostview_posts') ?: 4);

        return $collection;
    }

    /**
     * @return Collection
     */
    public function getRecentPost()
    {
        $collection = $this->helperData->getPostList();
        $collection->getSelect()
            ->limit((int)$this->helperData->getBlogConfig('sidebar/number_recent_posts') ?: 4);

        return $collection;
    }
}
