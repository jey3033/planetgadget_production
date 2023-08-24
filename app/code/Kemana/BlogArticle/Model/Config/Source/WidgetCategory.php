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

use Magento\Framework\Option\ArrayInterface;
use Kemana\Blog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class WidgetCategory
 * @package Kemana\Blog\Model\Config\Source\Widget
 */
class WidgetCategory implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $category;

    /**
     * WidgetCategory constructor.
     *
     * @param CollectionFactory $category
     */
    public function __construct(
        CollectionFactory $category
    ) {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->category->create();
        $ar = [];
        foreach ($collection->getItems() as $item) {
            if ($item->getId() === '1') {
                continue;
            }
            $ar[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $ar;
    }
}
