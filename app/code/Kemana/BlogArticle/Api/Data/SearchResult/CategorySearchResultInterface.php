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

namespace Kemana\Blog\Api\Data\SearchResult;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CategorySearchResultInterface
 * @package Kemana\Blog\Api\Data\SearchResult
 */
interface CategorySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Kemana\Blog\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Kemana\Blog\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
