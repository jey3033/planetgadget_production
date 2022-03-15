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

namespace Kemana\Promotion\Api;

use Kemana\Promotion\Api\Data;
use Kemana\Promotion\Api\Data\PromotionInterface;

/**
 * Interface PromotionRepositoryInterface
 * @package Kemana\Promotion\Api
 */
interface PromotionRepositoryInterface
{
    /**
     * Save a Promotion.
     *
     * @param \Kemana\Promotion\Api\Data\PromotionInterface $promotion
     * @return \Kemana\Promotion\Api\Data\PromotionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Kemana\Promotion\Api\Data\PromotionInterface $promotion);

    /**
     * Retrieve a Promotion.
     *
     * @param int $promotionId
     * @return \Kemana\Promotion\Api\Data\PromotionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($promotionId);

    /**
     * Retrieve Promotions matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Kemana\Promotion\Api\Data\PromotionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete a Promotion.
     *
     * @param \Kemana\Promotion\Api\Data\PromotionInterface $promotion
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Kemana\Promotion\Api\Data\PromotionInterface $promotion);

    /**
     * Delete a Promotion by ID.
     *
     * @param int $promotionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($promotionId);
}
