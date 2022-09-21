<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface PromotionSearchResultsInterface
 * @package Kemana\Promotion\Api\Data
 */
interface PromotionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * @param array $items
     * @return PromotionSearchResultsInterface
     */
    public function setItems(array $items);
}
