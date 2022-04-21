<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Common\Block\Home;

use Kemana\Promotion\Api\PromotionRepositoryInterface;
use Kemana\Blog\Api\BlogRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Kemana\Promotion\Helper\Data as PromotionHelper;
use Kemana\Common\Helper\Data as CommonHelper;

/**
 * Class Content
 */
class Content extends \Magento\Framework\View\Element\Template
{
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * @var BlogRepositoryInterface
     */
    protected $blogRepository;

    /**
     * @var PromotionHelper
     */
    protected $promotionHelper;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var CommonHelper
     */
    protected $commonHelper;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @param PromotionRepositoryInterface $promotionRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PromotionHelper $promotionHelper
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CommonHelper $commonHelper
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param BlogRepositoryInterface $blogRepository
     * @param Context $context
     */
    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PromotionHelper $promotionHelper,
        SortOrderBuilder $sortOrderBuilder,
        CommonHelper $commonHelper,
        FilterGroupBuilder $filterGroupBuilder,
        BlogRepositoryInterface $blogRepository,
        Context $context
    )
    {
        $this->promotionRepository = $promotionRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->promotionHelper = $promotionHelper;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->commonHelper = $commonHelper;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->blogRepository = $blogRepository;
        parent::__construct($context);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLatestBankPromotions()
    {
        $filtersStatus = $this->filterBuilder
            ->setField('is_active')
            ->setConditionType('eq')
            ->setValue($this->promotionHelper->getPromotionActiveStatus())
            ->create();

        $filterGroupStatus = $this->filterGroupBuilder
            ->addFilter($filtersStatus)
            ->create();

        $filtersImageExist = $this->filterBuilder
            ->setField('landing_image')
            ->setConditionType('notnull')
            ->create();

        $filterGroupImageExist = $this->filterGroupBuilder
            ->addFilter($filtersImageExist)
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection('DESC')
            ->create();

        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $this->searchCriteriaBuilder->setPageSize(3);

        $this->searchCriteriaBuilder->setFilterGroups([$filterGroupStatus, $filterGroupImageExist]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->promotionRepository->getList($searchCriteria);

        return $searchResults['searchResult']->getItems();
    }

    /**
     * @return \Kemana\Blog\Api\Data\PostInterface[]
     * @throws NoSuchEntityException
     */
    public function getLatestBlogNews()
    {
        $filters[] = $this->filterBuilder
            ->setField('image')
            ->setConditionType('notnull')
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField('created_at')
            ->setDirection('DESC')
            ->create();

        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $this->searchCriteriaBuilder->setPageSize(3);

        $this->searchCriteriaBuilder->addFilters($filters);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->blogRepository->getPostList($searchCriteria);

        return $searchResults->getItems();


    }

    /**
     * Get Media Folder Url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaFolderUrl(): string
    {
        return $this->commonHelper->getMediaFolderUrl();
    }

}
