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

namespace Kemana\Blog\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Post
 * @package Kemana\Blog\Block\Adminhtml
 */
class Post extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_post';
        $this->_blockGroup = 'Kemana_Blog';
        $this->_headerText = __('Posts');
        $this->_headerText = __('Posts');
        $this->_addButtonLabel = __('Create New Post');

        parent::_construct();
    }
}
