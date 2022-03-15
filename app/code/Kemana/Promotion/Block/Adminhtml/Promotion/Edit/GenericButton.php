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

namespace Kemana\Promotion\Block\Adminhtml\Promotion\Edit;

use Kemana\Promotion\Api\PromotionRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 * @package Kemana\Promotion\Block\Adminhtml\Promotion\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    public $context;

    /**
     * @var PromotionRepositoryInterface
     */
    public $promotionRepository;

    /**
     * GenericButton constructor.
     * @param Context $context
     * @param PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(
        Context $context,
        PromotionRepositoryInterface $promotionRepository
    )
    {
        $this->context = $context;
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockId()
    {
        try {
            return $this->promotionRepository->getById(
                $this->context->getRequest()->getParam('promotion_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
