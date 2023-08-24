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

namespace Kemana\Blog\Block\Adminhtml\Import\Edit;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Messages;
use Kemana\Blog\Helper\Data as BlogHelper;
use Kemana\Blog\Model\Config\Source\Import\Type;

/**
 * Class Import
 * @package Kemana\Blog\Block\Adminhtml\Import\Edit
 */
class Import extends Template
{
    /**
     * @var BlogHelper
     */
    public $blogHelper;

    /**
     * @var Type
     */
    public $importType;

    /**
     * Before constructor.
     *
     * @param Context $context
     * @param BlogHelper $blogHelper
     * @param Type $importType
     * @param array $data
     */
    public function __construct(
        Context $context,
        BlogHelper $blogHelper,
        Type $importType,
        array $data = []
    ) {
        $this->blogHelper = $blogHelper;
        $this->importType = $importType;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTypeSelector()
    {
        $types = [];
        foreach ($this->importType->toOptionArray() as $item) {
            $types[] = $item['value'];
        }
        array_shift($types);

        return $this->blogHelper->jsonEncode($types);
    }

    /**
     * @param $priority
     * @param $message
     *
     * @return string
     */
    public function getMessagesHtml($priority, $message)
    {
        /** @var $messagesBlock Messages */
        $messagesBlock = $this->_layout->createBlock(Messages::class);
        $messagesBlock->{$priority}(__($message));

        return $messagesBlock->toHtml();
    }

    /**
     * @return string
     */
    public function getImportButtonHtml()
    {
        $importUrl = $this->getUrl('kemana_blog/import/import');
        // $html = '&nbsp;&nbsp;<button id="word-press-import" href="' . $importUrl .
        //     '" class="" type="button" onclick="mpBlogImport.importAction();">' .
        //     '<span><span><span>Import</span></span></span></button>';
        $html = '&nbsp;&nbsp;<button id="word-press-import" href="' . $importUrl .
            '" class="" type="button">' .
            '<span><span><span>Import</span></span></span></button>';            

        return $html;
    }
}
