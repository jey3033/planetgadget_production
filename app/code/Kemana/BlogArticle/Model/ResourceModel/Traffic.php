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

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Traffic
 * @package Kemana\Blog\Model\ResourceModel
 */
class Traffic extends AbstractDb
{
    /**
     * Define main table
     */
    public function _construct()
    {
        $this->_init('kemana_blog_post_traffic', 'traffic_id');
    }
}
