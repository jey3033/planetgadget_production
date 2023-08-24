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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Kemana\Blog\Api\Data\CategoryInterface;
use Kemana\Blog\Helper\Data as HelperData;
use Kemana\Blog\Model\Category;
use Kemana\Blog\Model\CategoryFactory;
use Kemana\Blog\Model\ResourceModel\Category\Collection;
use Kemana\Blog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Widget
 * @package Kemana\Blog\Block\Category
 */
class Menu extends Template
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var CategoryFactory
     */
    protected $category;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CategoryFactory $categoryFactory
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CategoryFactory $categoryFactory,
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->categoryCollection = $collectionFactory;
        $this->category = $categoryFactory;
        $this->helper = $helperData;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @param $id
     *
     * @return CategoryInterface[]
     * @throws NoSuchEntityException
     */
    public function getChildCategory($id)
    {
        $collection = $this->categoryCollection->create()->addAttributeToFilter('parent_id', $id)
            ->addAttributeToFilter('enabled', '1');
        $this->helper->addStoreFilter($collection, $this->storeManager->getStore()->getId());

        return $collection->getItems();
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCollections()
    {
        $collection = $this->categoryCollection->create()
            ->addAttributeToFilter('level', '1')->addAttributeToFilter('enabled', '1');

        return $this->helper->addStoreFilter($collection, $this->storeManager->getStore()->getId());
    }

    /**
     * @param Category $parentCategory
     *
     * @return string
     */
    public function getMenuHtml($parentCategory)
    {
        $categoryUrl = $this->helper->getBlogUrl('category/' . $parentCategory->getUrlKey());
        $html = '<li class="level' . $parentCategory->getLevel()
            . ' category-item ui-menu-item" role="presentation">'
            . '<a href="' . $categoryUrl . '" class="ui-corner-all" tabindex="-1" role="menuitem">'
            . '<span>' . $parentCategory->getName() . '</span></a>';

        $childCategorys = $this->getChildCategory($parentCategory->getId());

        if (count($childCategorys) > 0) {
            $html .= '<ul class="level' . $parentCategory->getLevel() . ' submenu ui-menu ui-widget'
                . ' ui-widget-content ui-corner-all"'
                . ' role="menu" aria-expanded="false" style="display: none; top: 47px; left: -0.15625px;"'
                . ' aria-hidden="true">';

            /** @var Category $childCategory */
            foreach ($childCategorys as $childCategory) {
                $html .= $this->getMenuHtml($childCategory);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * @param Category $parentCategory
     *
     * @return string
     */
    public function getPortoMenuHtml($parentCategory)
    {
        $categoryUrl = $this->helper->getBlogUrl('category/' . $parentCategory->getUrlKey());
        $html = '<li class="ui-menu-item level' . $parentCategory->getLevel() . ' parent" role="presentation">'
            . '<div class="open-children-toggle"></div>'
            . '<a href="' . $categoryUrl . '" class="ui-corner-all" tabindex="-1" role="menuitem">'
            . '<span>' . $parentCategory->getName() . '</span></a>';

        $childCategories = $this->getChildCategory($parentCategory->getId());

        if (count($childCategories) > 0) {
            $html .= '<ul class="subchildmenu level' . $parentCategory->getLevel() . ''
                . ' ui-widget-content ui-corner-all"'
                . ' role="menu" aria-expanded="false"'
                . ' aria-hidden="true">';

            /** @var Category $childCategory */
            foreach ($childCategories as $childCategory) {
                $html .= $this->getMenuHtml($childCategory);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * @return string
     */
    public function getBlogHomePageTitle()
    {
        return $this->helper->getBlogConfig('general/name') ?: __('Blog');
    }

    /**
     * @return string
     */
    public function getBlogHomeUrl()
    {
        return $this->helper->getBlogUrl('');
    }
}
