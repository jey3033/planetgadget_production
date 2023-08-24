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

namespace Kemana\Blog\Model\Config\Source;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;
use Kemana\Blog\Model\AuthorFactory;

/**
 * Class Author
 * @package Kemana\Faqs\Model\Config\Source
 */
class Author implements ArrayInterface
{
    /**
     * @var AuthorFactory
     */
    public $_authorFactory;

    public function __construct(
        AuthorFactory $authorFactory
    ) {
        $this->_authorFactory = $authorFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAuthors() as $value => $author) {
            $options[] = [
                'value' => $value,
                'label' => $author->getName()
            ];
        }

        return $options;
    }

    /**
     * @return AbstractCollection
     */
    public function getAuthors()
    {
        return $this->_authorFactory->create()->getCollection()->addFieldToFilter('status', '1');
    }
}
