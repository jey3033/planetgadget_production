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

namespace Kemana\BlogArticle\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Url;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Category as CategoryOptions;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Tag as TagOptions;
use Kemana\Blog\Block\Adminhtml\Post\Edit\Tab\Renderer\Topic as TopicOptions;
use Kemana\BlogArticle\Helper\Data as HelperData;
use Kemana\Blog\Helper\Image;
use Kemana\Blog\Model\CategoryFactory;
use Kemana\Blog\Model\CommentFactory;
use Kemana\Blog\Model\Config\Source\AuthorStatus;
use Kemana\Blog\Model\LikeFactory;
use Kemana\Blog\Model\PostFactory;
use Kemana\Blog\Model\PostLikeFactory;

/**
 * Class Frontend
 *
 * @package Kemana\Blog\Block
 */
class Frontend extends Template
{
    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var StoreManagerInterface
     */
    public $store;

    /**
     * @var CommentFactory
     */
    public $cmtFactory;

    /**
     * @var LikeFactory
     */
    public $likeFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var
     */
    public $commentTree;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @var CategoryOptions
     */
    protected $categoryOptions;

    /**
     * @var TopicOptions
     */
    protected $topicOptions;

    /**
     * @var TagOptions
     */
    protected $tagOptions;

    /**
     * @var PostLikeFactory
     */
    protected $postLikeFactory;

    /**
     * @var AuthorStatus
     */
    protected $authorStatusType;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var EncryptorInterface
     */
    public $enc;

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
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->cmtFactory = $commentFactory;
        $this->likeFactory = $likeFactory;
        $this->customerRepository = $customerRepository;
        $this->helperData = $helperData;
        $this->coreRegistry = $coreRegistry;
        $this->dateTime = $dateTime;
        $this->categoryFactory = $categoryFactory;
        $this->postFactory = $postFactory;
        $this->customerUrl = $customerUrl;
        $this->postLikeFactory = $postLikeFactory;
        $this->categoryOptions = $category;
        $this->topicOptions = $topic;
        $this->tagOptions = $tag;
        $this->authorStatusType = $authorStatus;
        $this->themeProvider = $themeProvider;
        $this->store = $context->getStoreManager();
        $this->enc = $enc;

        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getBlogHelper()
    {
        return $this->helperData;
    }

    /**
     * @return bool
     */
    public function isBlogEnabled()
    {
        return $this->helperData->isEnabled();
    }

    /**
     * @param $content
     *
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter((string)$content);
    }

    /**
     * @param $image
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($image, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        $imageHelper = $this->helperData->getImageHelper();
        $imageFile = $imageHelper->getMediaPath($image, $type);

        return $this->helperData->getImageHelper()->getMediaUrl($imageFile);
    }

    /**
     * @param $urlKey
     * @param null $type
     *
     * @return string
     */
    public function getRssUrl($urlKey, $type = null)
    {
        if (is_object($urlKey)) {
            $urlKey = $urlKey->getUrlKey();
        }

        $urlKey = ($type ? $type . '/' : '') . $urlKey;
        $url = $this->helperData->getUrl($this->helperData->getRoute() . '/' . $urlKey);

        return rtrim($url, '/') . '.xml';
    }

    /**
     * @param $post
     *
     * @return Phrase|string
     * @throws Exception
     */
    public function getPostInfo($post)
    {
        $likeCollection = $this->postLikeFactory->create()->getCollection();
        $couldLike = $likeCollection->addFieldToFilter('post_id', $post->getId())
            ->addFieldToFilter('action', '1')->count();
        $html = __(
            '<i class="mp-blog-icon mp-blog-calendar-times"></i> %1',
            $this->getDateFormat($post->getPublishDate())
        );

        if ($categoryPost = $this->getPostTopicHtml($post)) {
            $html .= " | $categoryPost";
        }else {
            $html .= " | tidak ada topic";
        }

        $author = $this->helperData->getAuthorByPost($post);
        if ($author && $author->getName() && $this->helperData->showAuthorInfo()) {
            $aTag = '<a class="mp-info" href="' . $author->getUrl() . '">'
                . $this->escapeHtml($author->getName()) . '</a>';
            $html .= __(' | <i class="mp-blog-icon mp-blog-user"></i> %1', $aTag);
        }

        if ($this->getCommentinPost($post)) {
            $html .= __(
                ' | <i class="mp-blog-icon mp-blog-comments" aria-hidden="true"></i> %1',
                $this->getCommentinPost($post)
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

    /**
     * @param $post
     *
     * @return int
     */
    public function getCommentinPost($post)
    {
        $cmt = $this->cmtFactory->create()->getCollection()->addFieldToFilter('post_id', $post->getId());

        return $cmt->count();
    }

    /**
     * get list category html of post
     *
     * @param $post
     *
     * @return null|string
     */
    public function getPostCategoryHtml($post)
    {
        if (!$post->getCategoryIds()) {
            return null;
        }

        $categories = $this->helperData->getCategoryCollection($post->getCategoryIds());
        $categoryHtml = [];
        foreach ($categories as $_cat) {
            $categoryHtml[] = '<a class="mp-info" href="' . $this->helperData->getBlogUrl(
                $_cat,
                HelperData::TYPE_CATEGORY
            ) . '">' . $_cat->getName() . '</a>';
        }

        return implode(', ', $categoryHtml);
    }

    /**
     * @return mixed
     */
    public function getCurrentAuthor()
    {
        $authorId = "aa";
        // var_dump($authorId);die();
        // if ($authorId) {
        //     $author = $this->helperData->getObjectByParam($authorId, null, HelperData::TYPE_AUTHOR);
        //     if ($author && $author->getId()) {
        //         return $author;
        //     }
        // }

        return $authorId;
    }

    /**
     * get list topic html of post
     *
     * @param $post
     *
     * @return null|string
     */
    public function getPostTopicHtml($post)
    {
        if (!$post->getTopicIds()) {
            return null;
        }

        $categories = $this->helperData->getTopicCollection($post->getTopicIds());
        $categoryHtml = [];
        foreach ($categories as $_cat) {
            $categoryHtml[] = '<a class="mp-info" href="' . $this->helperData->getBlogUrl(
                $_cat,
                HelperData::TYPE_TOPIC
            ) . '">' . $_cat->getName() . '</a>';
        }

        return implode(', ', $categoryHtml);
    }

    /**
     * @param $date
     * @param bool $monthly
     *
     * @return false|string
     * @throws Exception
     */
    public function getDateFormat($date, $monthly = false)
    {
        return $this->helperData->getDateFormat($date, $monthly);
    }

    /**
     * @param $image
     * @param null $size
     * @param string $type
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function resizeImage($image, $size = null, $type = Image::TEMPLATE_MEDIA_TYPE_POST)
    {
        if (!$image) {
            return $this->getDefaultImageUrl();
        }

        return $this->helperData->getImageHelper()->resizeImage($image, $size, $type);
    }

    /**
     * get default image url
     */
    public function getDefaultImageUrl()
    {
        return $this->getViewFileUrl('Kemana_Blog::media/images/kemana-logo-default.png');
    }

    /**
     * @return string
     */
    public function getDefaultAuthorImage()
    {
        return $this->getViewFileUrl('Kemana_Blog::media/images/no-artist-image.jpg');
    }
}
