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

namespace Kemana\Blog\Block\Adminhtml\Topic;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Kemana\Blog\Model\Topic;

/**
 * Class Edit
 * @package Kemana\Blog\Block\Adminhtml\Topic
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Topic edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Kemana_Blog';
        $this->_controller = 'adminhtml_topic';

        parent::_construct();

        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * Retrieve text for header element depending on loaded Topic
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Topic $topic */
        $topic = $this->coreRegistry->registry('kemana_blog_topic');
        if ($topic->getId()) {
            return __("Edit Topic '%1'", $this->escapeHtml($topic->getName()));
        }

        return __('New Topic');
    }
}
