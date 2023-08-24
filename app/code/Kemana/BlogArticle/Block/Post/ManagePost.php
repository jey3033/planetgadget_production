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

namespace Kemana\Blog\Block\Post;

use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data;

/**
 * Class ManagePost
 * @package Kemana\Blog\Block\Post
 */
class ManagePost extends Frontend
{
    /**
     * @return string
     */
    public function getCategoriesTree()
    {
        return $this->helperData->jsonEncode($this->categoryOptions->getCategoriesTree());
    }

    /**
     * @return string
     */
    public function getTopicTree()
    {
        return $this->helperData->jsonEncode($this->topicOptions->getTopicsCollection());
    }

    /**
     * @return string
     */
    public function getTagTree()
    {
        return $this->helperData->jsonEncode($this->tagOptions->getTagsCollection());
    }

    /**
     * @return bool
     */
    public function checkTheme()
    {
        return $this->themeProvider->getThemeById($this->helperData->getCurrentThemeId())
                ->getCode() === 'Smartwave/porto';
    }
}
