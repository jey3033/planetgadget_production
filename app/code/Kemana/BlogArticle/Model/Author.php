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

namespace Kemana\Blog\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Kemana\Blog\Helper\Data;

/**
 * Class Author
 * @package Kemana\Blog\Model
 */
class Author extends AbstractModel
{
    /**
     * @inheritdoc
     */
    const CACHE_TAG = 'kemana_blog_author';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Author constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->helperData = $helperData;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Author::class);
    }

    /**
     * @param $name
     *
     * @return DataObject
     */
    public function getAuthorByName($name)
    {
        return $this->getCollection()->addFieldToFilter('name', $name)->getFirstItem();
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->helperData->getBlogUrl($this, Data::TYPE_AUTHOR);
    }

    /**
     * @return bool
     */
    public function hasPost()
    {
        try {
            return (bool)count($this->_getResource()->getPostIds($this));
        } catch (LocalizedException $exception) {
            return false;
        }
    }
}
