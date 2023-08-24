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

namespace Kemana\Blog\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;

/**
 * Class Edit
 * @package Kemana\Blog\Block\Adminhtml\Import
 */
class Edit extends Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');

        $this->buttonList->add(
            'check-connection',
            [
                'label' => __('Check Connection'),
                'class' => 'primary',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'target' => '#edit_form',

                        ]
                    ]
                ],
                // 'onclick' => 'mpBlogImport.initImportCheckConnection();'
            ],
            -100
        );
        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Kemana_Blog';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Import Setting');
    }
}
