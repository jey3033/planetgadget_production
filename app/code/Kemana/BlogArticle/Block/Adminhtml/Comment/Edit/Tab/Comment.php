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

namespace Kemana\Blog\Block\Adminhtml\Comment\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;
use Kemana\Blog\Model\Config\Source\Comments\Status;
use Kemana\Blog\Model\PostFactory;

/**
 * Class Comment
 * @package Kemana\Blog\Block\Adminhtml\Comment\Edit\Tab
 */
class Comment extends Generic implements TabInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @var PostFactory
     */
    protected $_postFactory;

    /**
     * @var Status
     */
    protected $_commentStatus;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Comment constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param PostFactory $postFactory
     * @param Status $commentStatus
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CustomerRepositoryInterface $customerRepository,
        PostFactory $postFactory,
        Status $commentStatus,
        Store $systemStore,
        array $data = []
    ) {
        $this->_commentStatus = $commentStatus;
        $this->_customerRepository = $customerRepository;
        $this->_postFactory = $postFactory;
        $this->systemStore = $systemStore;
        $this->storeManager = $context->getStoreManager();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        $comment = $this->_coreRegistry->registry('kemana_blog_comment');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Comment Details'), 'class' => 'fieldset-wide']
        );

        if ($comment->getId()) {
            $fieldset->addField('comment_id', 'hidden', ['name' => 'comment_id']);
        }

        $post = $this->_postFactory->create()->load($comment->getPostId());
        $postText = '<a href="' . $this->getUrl('kemana_blog/post/edit', ['id' => $comment->getPostId()])
            . '" onclick="this.target=\'blank\'">' . $this->escapeHtml($post->getName()) . '</a>';
        $fieldset->addField('post_name', 'note', ['text' => $postText, 'label' => __('Post'), 'name' => 'post_name']);

        if ($comment->getEntityId() > 0) {
            $customer = $this->_customerRepository->getById($comment->getEntityId());
            $customerText = '<a href="'
                . $this->getUrl(
                    'customer/index/edit',
                    ['id' => $customer->getId(), 'active_tab' => 'review']
                )
                . '" onclick="this.target=\'blank\'">'
                . $this->escapeHtml($customer->getFirstname() . ' ' . $customer->getLastname())
                . '</a> <a href="mailto:%4">(' . $customer->getEmail() . ')</a>';
        } else {
            $customerText = 'Guest';
        }

        $fieldset->addField(
            'customer_name',
            'note',
            ['text' => $customerText, 'label' => __('Customer'), 'name' => 'customer_name']
        );

        $fieldset->addField('status', 'select', [
            'label' => __('Status'),
            'required' => true,
            'name' => 'status',
            'values' => $this->_commentStatus->toArray()
        ]);
        $fieldset->addField('content', 'textarea', [
            'label' => __('Content'),
            'required' => true,
            'name' => 'content',
            'style' => 'height:24em;'
        ]);
        $viewText = '';
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getId() === $comment->getStoreIds()) {
                $viewText .= '<a href="' . $post->getUrl($store->getId()) . '#cmt-id-' . $comment->getId()
                    . '" onclick="this.target=\'blank\'">View in store ' . $store->getName() . '</a><br>';
            }
        }

        $fieldset->addField(
            'view_front',
            'note',
            ['text' => $viewText, 'label' => __('View On Front End'), 'name' => 'view_front']
        );

        $form->addValues($comment->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Comment');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
