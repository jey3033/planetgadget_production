<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Catalog
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Catalog\Block\Product\ProductList;

/**
 * Product list toolbar
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    
    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if (($this->getCurrentOrder()) == 'position') {
                $this->_collection->addAttributeToSort(
                    $this->getCurrentOrder(),
                    $this->getCurrentDirection()
                );
            }else if (($this->getCurrentOrder()) == 'Highest') {
                $this->_collection->addAttributeToSort(
                    'price', 'desc',
                    $this->getCurrentDirectionReverse()
                );
            }else if (($this->getCurrentOrder()) == 'Lowest') {
                $this->_collection->addAttributeToSort(
                    'price', 'asc',
                    $this->getCurrentDirection()
                );
            } else {
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
        return $this;
    }
}
