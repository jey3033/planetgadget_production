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

use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\AuthorFactory;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package Kemana\Blog\Block\Author
 */
class Listpost extends \Kemana\Blog\Block\Listpost
{
    /**
     * @var AuthorFactory
     */
    protected $_author;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if ($author = $this->getAuthor()) {
            return $this->helperData->getPostCollection(Data::TYPE_AUTHOR, $author->getId());
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        if (!$this->_author) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $author = $this->helperData->getObjectByParam($id, null, Data::TYPE_AUTHOR);
                if ($author && $author->getId()) {
                    $this->_author = $author;
                }
            }
        }

        return $this->_author;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $author = $this->getAuthor();
            if ($author) {
                $breadcrumbs->addCrumb($author->getUrlKey(), [
                    'label' => __('Author'),
                    'title' => __('Author')
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
        $author = $this->getAuthor();
        if (!$author) {
            return $blogTitle;
        }

        if ($meta) {
            array_push($blogTitle, ucfirst($author->getName()));

            return $blogTitle;
        }

        return ucfirst($author->getName());
    }
}
