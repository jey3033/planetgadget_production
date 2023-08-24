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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\Post;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class AuthorPost
 * @package Kemana\Blog\Block\Post
 */
class AuthorPost extends \Kemana\Blog\Block\Listpost
{

    /**
     * @return AbstractCollection|Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPostCollection()
    {
        $collection = $this->helperData->getFactoryByType()->create()->getCollection();
        $this->helperData->addStoreFilter($collection, $this->store->getStore()->getId());

        $userId = $this->getAuthor()->getId();

        $collection->addFieldToFilter('author_id', $userId);

        if ($collection && $collection->getSize()) {
            $pager = $this->getLayout()->createBlock(Pager::class, 'mpblog.post.pager');

            $perPageValues = (string)$this->helperData->getConfigGeneral('pagination');
            $perPageValues = explode(',', $perPageValues);
            $perPageValues = array_combine($perPageValues, $perPageValues);

            $pager->setAvailableLimit($perPageValues)
                ->setCollection($collection);

            $this->setChild('pager', $pager);
        }

        return $collection;
    }

    /**
     * @param $statusId
     *
     * @return mixed
     */
    public function getStatusHtmlById($statusId)
    {
        $statusText = $this->authorStatusType->toArray()[$statusId]->getText();

        switch ($statusId) {
            case '2':
                $html = '<div class="mp-post-status mp-post-disapproved">' . $statusText . '</div>';
                break;
            case '1':
                $html = '<div class="mp-post-status mp-post-approved">' . $statusText . '</div>';
                break;
            case '0':
            default:
                $html = '<div class="mp-post-status mp-post-pending">' . $statusText . '</div>';
                break;
        }

        return $html;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $array = explode('/', $this->helperData->getCmsWysiwygEditor());
        if ($array[count($array) - 1] === 'tinymce4Adapter') {
            return 4;
        }

        return 3;
    }

    /**
     * @return int
     */
    public function getMagentoVersion()
    {
        return (int)$this->helperData->versionCompare('2.3.0') ? 3 : 2;
    }

    /**
     * @param $postCollection
     *
     * @return string
     * @throws LocalizedException
     */
    public function getPostDatas($postCollection)
    {
        $result = [];

        /** @var Post $post */
        foreach ($postCollection->getItems() as $post) {
            $post->getCategoryIds();
            $post->getTopicIds();
            $post->getTagIds();
            if ($post->getPostContent()) {
                $post->setData('post_content', $this->getPageFilter($post->getPostContent()));
            }
            $result[$post->getId()] = $post->getData();
        }

        return $this->helperData->jsonEncode($result);
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->getAuthor()->getName();
    }

    /**
     * @return bool
     */
    public function getAuthorStatus()
    {
        $author = $this->getAuthor();

        return $author->getStatus() === '1';
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->coreRegistry->registry('mp_author');
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getBlogTitle($meta = false)
    {
        return $meta ? [$this->getAuthor()->getName()] : $this->getAuthor()->getName();
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
