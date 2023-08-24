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

namespace Kemana\Blog\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Posts
 * @package Kemana\Blog\Block\Widget
 */
class Posts extends Frontend implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/posts.phtml";

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCollection()
    {
        if ($this->hasData('show_type') && $this->getData('show_type') === 'category') {
            $collection = $this->helperData->getObjectByParam($this->getData('category_id'), null, Data::TYPE_CATEGORY)
                ->getSelectedPostsCollection();
            $this->helperData->addStoreFilter($collection);
        } else {
            $collection = $this->helperData->getPostList();
        }

        $collection->setPageSize($this->getData('post_count'));

        return $collection;
    }

    /**
     * @return Data
     */
    public function getHelperData()
    {
        return $this->helperData;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * @param $code
     *
     * @return string
     */
    public function getBlogUrl($code)
    {
        return $this->helperData->getBlogUrl($code);
    }
}
