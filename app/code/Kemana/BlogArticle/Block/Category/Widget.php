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

use Exception;
use Kemana\Blog\Block\Adminhtml\Category\Tree as CategoryTree;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category as CategoryOptions;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag as TagOptions;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Topic as TopicOptions;
use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data;
use Kemana\Blog\Helper\Data as HelperData;
use Kemana\Blog\Model\CategoryFactory;
use Kemana\Blog\Model\CommentFactory;
use Kemana\Blog\Model\Config\Source\AuthorStatus;
use Kemana\Blog\Model\LikeFactory;
use Kemana\Blog\Model\PostFactory;
use Kemana\Blog\Model\PostLikeFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Widget
 * @package Kemana\Blog\Block\Category
 */
class Widget extends Frontend
{
    /**
     * @var CategoryTree
     */
    protected $categoryTree;

    /**
     * Frontend constructor.
     *
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param CommentFactory $commentFactory
     * @param LikeFactory $likeFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Registry $coreRegistry
     * @param HelperData $helperData
     * @param Url $customerUrl
     * @param CategoryFactory $categoryFactory
     * @param PostFactory $postFactory
     * @param DateTime $dateTime
     * @param PostLikeFactory $postLikeFactory
     * @param CategoryOptions $category
     * @param TopicOptions $topic
     * @param TagOptions $tag
     * @param ThemeProviderInterface $themeProvider
     * @param EncryptorInterface $enc
     * @param AuthorStatus $authorStatus
     * @param CategoryTree $categoryTree
     * @param array $data
     */
    public function __construct(
        Context $context,
        FilterProvider $filterProvider,
        CommentFactory $commentFactory,
        LikeFactory $likeFactory,
        CustomerRepositoryInterface $customerRepository,
        Registry $coreRegistry,
        HelperData $helperData,
        Url $customerUrl,
        CategoryFactory $categoryFactory,
        PostFactory $postFactory,
        DateTime $dateTime,
        PostLikeFactory $postLikeFactory,
        CategoryOptions $category,
        TopicOptions $topic,
        TagOptions $tag,
        ThemeProviderInterface $themeProvider,
        EncryptorInterface $enc,
        AuthorStatus $authorStatus,
        CategoryTree $categoryTree,
        array $data = []
    ) {
        $this->categoryTree = $categoryTree;

        parent::__construct(
            $context,
            $filterProvider,
            $commentFactory,
            $likeFactory,
            $customerRepository,
            $coreRegistry,
            $helperData,
            $customerUrl,
            $categoryFactory,
            $postFactory,
            $dateTime,
            $postLikeFactory,
            $category,
            $topic,
            $tag,
            $themeProvider,
            $enc,
            $authorStatus,
            $data
        );
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getTree()
    {
        $tree = $this->categoryTree->getTree(null, $this->store->getStore()->getId());
        return $tree;
    }

    /**
     * @param $tree
     *
     * @return Phrase|string
     */
    public function getCategoryTreeHtml($tree)
    {
        if (!$tree) {
            return __('No Categories.');
        }

        $html = '';
        foreach ($tree as $value) {
            if (!$value) {
                continue;
            }
            if ($value['enabled']) {
                $level    = count(explode('/', ($value['path'])));
                $hasChild = isset($value['children']) && $level < 4;
                $html .= '<ul class="block-content menu-categories category-level'
                    . $level . '" style="margin-bottom:0px;margin-top:8px;">';
                $html .= '<li class="category-item">';
                $html .= $hasChild ? '<i class="fa fa-plus-square mp-blog-expand-tree-' . $level . '"></i>' : '';
                $html .= '<a class="list-categories" href="' . $this->getCategoryUrl($value['url']) . '">';
                $html .= '<i class="fa fa-folder-open">&nbsp;&nbsp;</i>';
                $html .= ucfirst($value['text']) . '</a>';
                $html .= $hasChild ? $this->getCategoryTreeHtml($value['children']) : '';
                $html .= '</li>';
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->helperData->getBlogUrl($category, Data::TYPE_CATEGORY);
    }
}
