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

namespace Kemana\Blog\Block\Tag;

use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\ResourceModel\Post\Collection;
use Kemana\Blog\Model\TagFactory;

/**
 * Class Listpost
 * @package Kemana\Blog\Block\Tag
 */
class Listpost extends \Kemana\Blog\Block\Listpost
{
    /**
     * @var TagFactory
     */
    protected $_tag;

    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        if ($tag = $this->getBlogObject()) {
            return $this->helperData->getPostCollection(Data::TYPE_TAG, $tag->getId());
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getBlogObject()
    {
        if (!$this->_tag) {
            $id = $this->getRequest()->getParam('id');

            if ($id) {
                $tag = $this->helperData->getObjectByParam($id, null, Data::TYPE_TAG);
                if ($tag && $tag->getId()) {
                    $this->_tag = $tag;
                }
            }
        }

        return $this->_tag;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $tag = $this->getBlogObject();
            if ($tag) {
                $breadcrumbs->addCrumb($tag->getUrlKey(), [
                    'label' => __('Tag'),
                    'title' => __('Tag')
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
        $tag = $this->getBlogObject();
        if (!$tag) {
            return $blogTitle;
        }

        if ($meta) {
            if ($tag->getMetaTitle()) {
                array_push($blogTitle, $tag->getMetaTitle());
            } else {
                array_push($blogTitle, ucfirst($tag->getName()));
            }

            return $blogTitle;
        }

        return ucfirst($tag->getName());
    }
}
