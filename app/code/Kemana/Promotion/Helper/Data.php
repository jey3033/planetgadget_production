<?php
/*/**
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

namespace Kemana\Promotion\Helper;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Kemana\Promotion\Api\PromotionRepositoryInterface;

/**
 * Class Data
 * @package Kemana\Promotion\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check module enable or not
     */
    const XML_PATH_PROMOTION_IS_ENABLE = 'promotion/general/is_enabled';

    /**
     * Landing Page Title
     */
    const XML_PATH_PROMOTION_LANDING_PAGE_TITLE = 'promotion/general/landing_page_title';

    /**
     * Get the custom front url name for landing page
     */
    const XML_PATH_PROMOTION_LANDING_PAGE_FRONT_NAME = 'promotion/general/landing_page_url';

    /**
     * Get the promotion short content length for promotion landing page
     */
    const XML_PATH_PROMOTION_LANDING_PAGE_SHORT_CONTENT_LENGTH = 'promotion/general/promotion_short_content_length_on_landing_page';

    /**
     * Get the promotions per page
     */
    const XML_PATH_PROMOTION_PER_PAGE = 'promotion/general/promotions_per_page';

    /**
     * Get the promotion grid allowed values for pager
     */
    const XML_PATH_PROMOTION_GRID_ALLOWED_VALUES = 'catalog/frontend/grid_per_page_values';

    /**
     * Active status of promotion table
     */
    const ACTIVE_STATUS = 1;

    /**
     * All store ID in store drop down
     */
    const ALL_STORE_ID = '0';

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var FilterGroup
     */
    protected $filterGroup;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Data constructor.
     *
     * @param FilterGroup $filterGroup
     * @param Filter $filter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param PromotionRepositoryInterface $promotionRepository
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        FilterGroup $filterGroup,
        Filter $filter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        PromotionRepositoryInterface $promotionRepository,
        FilterBuilder $filterBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->promotionRepository = $promotionRepository;
        $this->storeManager = $storeManager;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    /**
     * @return mixed
     */
    public function promotionIsEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_IS_ENABLE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getLandingPageFrontName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_LANDING_PAGE_FRONT_NAME, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getLandingPageTitle()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_LANDING_PAGE_TITLE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getPromotionPerPage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_PER_PAGE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getPromotionGridPagerAllowedValues()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_GRID_ALLOWED_VALUES, $this->storeScope);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPromotionShortContentLength()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_LANDING_PAGE_SHORT_CONTENT_LENGTH, $this->storeScope);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaFolderUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get promotionId by identifier
     *
     * @param $identifier
     * @param null $promotionId
     * @param bool $isSanitized
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPromotionIdForIdentifier($identifier, $promotionId = null, $isSanitized = true)
    {
        if (!$isSanitized) {
            $identifier = current(explode("/", $identifier));
        }

        if ($promotionId) {
            $filterPromotionId = $this->filterBuilder->setField('promotion_id')
                ->setValue($promotionId)
                ->setConditionType('neq')
                ->create();

            $filterGroupPromotionId = $this->filterGroupBuilder
                ->addFilter($filterPromotionId)
                ->create();
        }

        $filterStatus = $this->filterBuilder->setField('is_active')
            ->setValue(self::ACTIVE_STATUS)
            ->setConditionType('eq')
            ->create();

        $filterGroupStatus = $this->filterGroupBuilder
            ->addFilter($filterStatus)
            ->create();

        $filterIdentifier = $this->filterBuilder->setField('identifier')
            ->setValue($identifier)
            ->setConditionType('eq')
            ->create();

        $filterGroupIdentifier = $this->filterGroupBuilder
            ->addFilter($filterIdentifier)
            ->create();

        $filterStores = $this->filterBuilder->setField('stores')
            ->setValue('%' . $this->storeManager->getStore()->getId() . '%')
            ->setConditionType('like')
            ->create();
        $filterAllStores = $this->filterBuilder->setField('stores')
            ->setValue(self::ALL_STORE_ID)
            ->setConditionType('eq')
            ->create();

        $filterGroupStores = $this->filterGroupBuilder
            ->addFilter($filterStores)
            ->addFilter($filterAllStores)
            ->create();

        $finalFilterGroupSet = [$filterGroupStatus, $filterGroupIdentifier, $filterGroupStores];

        if ($promotionId) {
            array_push($finalFilterGroupSet, $filterGroupPromotionId);
        }

        $this->searchCriteriaBuilder->setFilterGroups($finalFilterGroupSet);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setPageSize(1)
            ->setCurrentPage(1);

        return $this->promotionRepository->getList($searchCriteria)['searchResult']->getItems();

    }

    /**
     * @return int
     */
    public function getPromotionActiveStatus(): int
    {
        return self::ACTIVE_STATUS;
    }
}
