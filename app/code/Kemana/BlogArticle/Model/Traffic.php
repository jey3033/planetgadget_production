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

use Magento\Framework\Model\AbstractModel;

/**
 * Class Traffic
 * @package Kemana\Blog\Model
 */
class Traffic extends AbstractModel
{
    /**
     * Define resource model
     */
    public function _construct()
    {
        $this->_init(ResourceModel\Traffic::class);
    }
}
