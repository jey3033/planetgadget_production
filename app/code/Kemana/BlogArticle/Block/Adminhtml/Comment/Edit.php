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

namespace Kemana\Blog\Block\Adminhtml\Comment;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Kemana\Blog\Model\Comment;
use Kemana\Blog\Model\Post;

/**
 * Class Edit
 * @package Kemana\Blog\Block\Adminhtml\Comment
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
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Kemana_Blog';
        $this->_controller = 'adminhtml_comment';

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
        $this->buttonList->update('save', 'label', 'Save Comment');
        $this->buttonList->remove('reset');
    }

    /**
     * Retrieve text for header element depending on loaded Post
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Comment $comment */
        $comment = $this->coreRegistry->registry('kemana_blog_comment');
        if ($comment->getId()) {
            return __("Edit Comment");
        }

        return __('New Comment');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        /** @var Post $post */
        $comment = $this->coreRegistry->registry('kemana_blog_comment');
        if ($id = $comment->getId()) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }

        return parent::getFormActionUrl();
    }
}
