<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Banner
 * @package Kemana\Banner\Block\Adminhtml
 */
class Banner extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_banner';
        $this->_blockGroup = 'Kemana_Banner';
        $this->_headerText = __('Banners');
        $this->_addButtonLabel = __('Create New Banner');

        parent::_construct();
    }
}
