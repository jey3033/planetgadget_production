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

namespace Kemana\Blog\Block\Category;

use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package Kemana\Blog\Block\Category
 */
class Listpost extends \Kemana\Blog\Block\Listpost
{
    /**
     * @var string
     */
    protected $_category;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if ($category = $this->getBlogObject()) {
            return $this->helperData->getPostCollection(Data::TYPE_CATEGORY, $category->getId());
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getBlogObject()
    {
        if (!$this->_category) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $category = $this->helperData->getObjectByParam($id, null, Data::TYPE_CATEGORY);
                if ($category && $category->getId()) {
                    $this->_category = $category;
                }
            }
        }

        return $this->_category;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $category = $this->getBlogObject();
            if ($category) {
                $breadcrumbs->addCrumb($category->getUrlKey(), [
                    'label' => __('Category'),
                    'title' => __('Category')
                ]);
            }
        }
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);
        $category = $this->getBlogObject();
        if (!$category) {
            return $blogTitle;
        }

        if ($meta) {
            if ($category->getMetaTitle()) {
                array_push($blogTitle, $category->getMetaTitle());
            } else {
                array_push($blogTitle, ucfirst($category->getName()));
            }

            return $blogTitle;
        }

        return ucfirst($category->getName());
    }
}
