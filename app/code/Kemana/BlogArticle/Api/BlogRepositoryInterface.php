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

namespace Kemana\Blog\Api;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class PostInterface
 * @package Kemana\Blog\Api
 */
interface BlogRepositoryInterface
{
    /**
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getAllPost();

    /**
     * @param string $postId
     *
     * @return \Kemana\Blog\Api\Data\PostInterface
     */
    public function getPostView($postId);

    /**
     * @param string $authorName
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostViewByAuthorName($authorName);

    /**
     * @param string $authorId
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostViewByAuthorId($authorId);

    /**
     * @param string $postId
     *
     * @return \Kemana\Blog\Api\Data\CommentInterface[]
     */
    public function getPostComment($postId);

    /**
     * Get All Comment
     *
     * @return \Kemana\Blog\Api\Data\CommentInterface[]
     */
    public function getAllComment();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Kemana\Blog\Api\Data\SearchResult\CommentSearchResultInterface
     */
    public function getCommentList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $commentId
     *
     * @return \Kemana\Blog\Api\Data\CommentInterface
     */
    public function getCommentView($commentId);

    /**
     * @param string $postId
     *
     * @return string
     */
    public function getPostLike($postId);

    /**
     * @param string $tagName
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostByTagName($tagName);

    /**
     * @param string $postId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProductByPost($postId);

    /**
     * @param string $postId
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostRelated($postId);

    /**
     * @param string $postId
     * @param \Kemana\Blog\Api\Data\CommentInterface $commentData
     *
     * @return \Kemana\Blog\Api\Data\CommentInterface
     * @throws Exception
     */
    public function addCommentInPost($postId, $commentData);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Kemana\Blog\Api\Data\SearchResult\PostSearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getPostList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Kemana\Blog\Api\Data\PostInterface $post
     *
     * @return \Kemana\Blog\Api\Data\PostInterface
     * @throws Exception
     */
    public function createPost($post);

    /**
     * Delete Post
     *
     * @param string $postId
     *
     * @return string
     */
    public function deletePost($postId);

    /**
     * @param string $postId
     * @param \Kemana\Blog\Api\Data\PostInterface $post
     *
     * @return \Kemana\Blog\Api\Data\PostInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updatePost($postId, $post);

    /**
     * Get All Tag
     *
     * @return \Kemana\Blog\Api\Data\TagInterface[]
     */
    public function getAllTag();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Kemana\Blog\Api\Data\SearchResult\TagSearchResultInterface
     */
    public function getTagList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create Post
     *
     * @param \Kemana\Blog\Api\Data\TagInterface $tag
     *
     * @return \Kemana\Blog\Api\Data\TagInterface
     * @throws Exception
     */
    public function createTag($tag);

    /**
     * Delete Tag
     *
     * @param string $tagId
     *
     * @return string
     */
    public function deleteTag($tagId);

    /**
     * @param string $tagId
     *
     * @return \Kemana\Blog\Api\Data\TagInterface
     */
    public function getTagView($tagId);

    /**
     * @param string $tagId
     * @param \Kemana\Blog\Api\Data\TagInterface $tag
     *
     * @return \Kemana\Blog\Api\Data\TagInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTag($tagId, $tag);

    /**
     * Get Topic List
     *
     * @return \Kemana\Blog\Api\Data\TopicInterface[]
     */
    public function getAllTopic();

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Kemana\Blog\Api\Data\SearchResult\TopicSearchResultInterface
     */
    public function getTopicList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $topicId
     *
     * @return \Kemana\Blog\Api\Data\TagInterface
     */
    public function getTopicView($topicId);

    /**
     * @param string $topicId
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByTopic($topicId);

    /**
     * Create Topic
     *
     * @param \Kemana\Blog\Api\Data\TopicInterface $topic
     *
     * @return \Kemana\Blog\Api\Data\TopicInterface
     * @throws Exception
     */
    public function createTopic($topic);

    /**
     * Delete Topic
     *
     * @param string $topicId
     *
     * @return string
     */
    public function deleteTopic($topicId);

    /**
     * @param string $topicId
     * @param \Kemana\Blog\Api\Data\TopicInterface $topic
     *
     * @return \Kemana\Blog\Api\Data\TopicInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateTopic($topicId, $topic);

    /**
     * Get All Category
     *
     * @return \Kemana\Blog\Api\Data\CategoryInterface[]
     */
    public function getAllCategory();

    /**
     * Get Category List
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Kemana\Blog\Api\Data\SearchResult\CategorySearchResultInterface
     */
    public function getCategoryList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param string $categoryId
     *
     * @return \Kemana\Blog\Api\Data\CategoryInterface
     */
    public function getCategoryView($categoryId);

    /**
     * @param string $categoryId
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByCategoryId($categoryId);

    /**
     * @param string $categoryKey
     *
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     */
    public function getPostsByCategory($categoryKey);

    /**
     * @param string $postId
     *
     * @return \Kemana\Blog\Api\Data\CategoryInterface[]
     */
    public function getCategoriesByPostId($postId);

    /**
     * Create Category
     *
     * @param \Kemana\Blog\Api\Data\CategoryInterface $category
     *
     * @return \Kemana\Blog\Api\Data\CategoryInterface
     * @throws Exception
     */
    public function createCategory($category);

    /**
     * Delete Category
     *
     * @param string $categoryId
     *
     * @return string
     */
    public function deleteCategory($categoryId);

    /**
     * @param string $categoryId
     * @param \Kemana\Blog\Api\Data\CategoryInterface $category
     *
     * @return \Kemana\Blog\Api\Data\CategoryInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateCategory($categoryId, $category);

    /**
     * Get Author List
     *
     * @return \Kemana\Blog\Api\Data\AuthorInterface[]
     */
    public function getAuthorList();

    /**
     * Create Author
     *
     * @param \Kemana\Blog\Api\Data\AuthorInterface $author
     *
     * @return \Kemana\Blog\Api\Data\AuthorInterface
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAuthor($author);

    /**
     * Delete Author
     *
     * @param string $authorId
     *
     * @return string
     */
    public function deleteAuthor($authorId);

    /**
     * @param string $authorId
     * @param \Kemana\Blog\Api\Data\AuthorInterface $author
     *
     * @return \Kemana\Blog\Api\Data\AuthorInterface
     * @throws InputException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function updateAuthor($authorId, $author);
}
