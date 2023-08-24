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

namespace Kemana\Blog\Model\ResourceModel\PostLike;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Kemana\Blog\Model\PostLike;

/**
 * Class Collection
 * @package Kemana\Blog\Model\ResourceModel\PostLike
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(PostLike::class, \Kemana\Blog\Model\ResourceModel\PostLike::class);
    }
}
