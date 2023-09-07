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

namespace Kemana\BlogArticle\Block\Author;

use Kemana\BlogArticle\Block\Frontend;
use Kemana\BlogArticle\Helper\Data;

/**
 * Class Widget
 * @package Kemana\Blog\Block\Author
 */
class Widget extends Frontend
{
    /**
     * @return mixed
     */
    public function getCurrentAuthor()
    {
        $authorId = $this->getRequest()->getParam('id');
        if ($authorId) {
            $author = $this->helperData->getObjectByParam($authorId, null, Data::TYPE_AUTHOR);
            if ($author && $author->getId()) {
                return $author;
            }
        }

        return null;
    }
}
