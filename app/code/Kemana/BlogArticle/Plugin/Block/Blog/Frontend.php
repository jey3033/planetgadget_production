<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_BlogArticle
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\BlogArticle\Plugin\Block\Blog;

use Kemana\Blog\Model\PostLikeFactory;
use Kemana\BlogArticle\Helper\Data as BlogArticleHelper;

/**
 * Class Frontend
 */
class Frontend
{

    /**
     * @var PostLikeFactory
     */
    protected $_postLikeFactory;

    /**
     * @var BlogArticleHelper
     */
    protected $_blogHelper;

    /**
     * @param PostLikeFactory $postLikeFactory
     * @param BlogArticleHelper $blogHelper
     */
    public function __construct(
        PostLikeFactory $postLikeFactory,
        BlogArticleHelper $blogHelper
    )
    {
        $this->_postLikeFactory = $postLikeFactory;
        $this->_blogHelper = $blogHelper;
    }

    /**
     * @param \Kemana\Blog\Block\Frontend $subject
     * @param $result
     * @param $post
     * @return mixed
     */
    public function afterGetPostInfo(\Kemana\Blog\Block\Frontend $subject, $result, $post) {

        $likeCollection = $this->_postLikeFactory->create()->getCollection();
        $couldLike = $likeCollection->addFieldToFilter('post_id', $post->getId())
            ->addFieldToFilter('action', '1')->count();
        $html = __(
            '<i class="mp-blog-icon mp-blog-calendar-times"></i> %1',
            $this->_blogHelper->getTimeAccordingToTimeZone($post->getPublishDate(), 'd M Y')
        );

        if ($categoryPost = $subject->getPostCategoryHtml($post)) {
            $html .= __(' | %1', $categoryPost);
        }

        $author = $subject->helperData->getAuthorByPost($post);
        if ($author && $author->getName() && $subject->helperData->showAuthorInfo()) {
            $aTag = '<a class="mp-info" href="' . $author->getUrl() . '">'
                . $subject->escapeHtml($author->getName()) . '</a>';
            $html .= __(' | <i class="mp-blog-icon mp-blog-user"></i> %1', $aTag);
        }

        if ($subject->getCommentinPost($post)) {
            $html .= __(
                ' | <i class="mp-blog-icon mp-blog-comments" aria-hidden="true"></i> %1',
                $subject->getCommentinPost($post)
            );
        }

        if ($post->getViewTraffic()) {
            $html .= __(
                ' | <i class="mp-blog-icon mp-blog-traffic" aria-hidden="true"></i> %1',
                $post->getViewTraffic()
            );
        }

        if ($couldLike > 0) {
            $html .= __(' | <i class="mp-blog-icon mp-blog-thumbs-up" aria-hidden="true"></i> %1', $couldLike);
        }

        return $html;
    }

}
