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

namespace Kemana\Blog\Block\Adminhtml\Category;

use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\Category;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Kemana\Blog\Block\Adminhtml\Category
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
     * @var Data
     */
    protected $_helperData;

    /**
     * Edit constructor.
     *
     * @param Registry $coreRegistry
     * @param Data $helperData
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Data $helperData,
        Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_helperData  = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * prepare the form
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Kemana_Blog';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        /** @var Category $category */
        $category = $this->coreRegistry->registry('category');

        if ($category->getId() && !$category->getDuplicate()) {
            $this->buttonList->add(
                'duplicate',
                [
                    'label'   => __('Duplicate'),
                    'class'   => 'duplicate',
                    'onclick' => sprintf("location.href = '%s';", $this->getDuplicateUrl()),
                ],
                -101
            );
        }

        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
    }

    /**
     * @return string
     */
    protected function getDuplicateUrl()
    {
        /** @var Category $category */
        $category = $this->coreRegistry->registry('category');

        return $this->getUrl(
            '*/*/duplicate',
            ['id' => $category->getId(), 'duplicate' => true, 'parent' => $category->getParentId()]
        );
    }

    /**
     * @return int
     */
    public function getMagentoVersion()
    {
        return (int) $this->_helperData->versionCompare('2.3.0') ? '4' : '';
    }

    /**
     * Gets the category identifier.
     *
     * @return     int   The category identifier.
     */
    public function getCategoryId()
    {
        $category   = $this->coreRegistry->registry('category');
        $categoryId = 0;
        if (!empty($category)) {
            $categoryId = $category->getId();
        }
        return $categoryId;
    }

    /**
     * Gets the reset url.
     *
     * @return     string  The reset url.
     */
    public function getResetUrl()
    {
        $resetPath = $this->getCategoryId() ? 'kemana_blog/*/edit' : 'kemana_blog/*/add';
        return $this->getUrl($resetPath, ['form_key' => $this->getFormKey()]);
    }

    /**
     * Gets the delete url.
     *
     * @return     string  The delete url.
     */
    public function getDeleteUrl()
    {
        $deletePath = $this->getCategoryId() ? 'kemana_blog/*/delete' : '';
        return $this->getUrl($deletePath, ['_current' => true, 'form_key' => $this->getFormKey()]);
    }
}
