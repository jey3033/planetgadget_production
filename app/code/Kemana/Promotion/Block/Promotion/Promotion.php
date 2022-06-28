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

namespace Kemana\Promotion\Block\Promotion;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Kemana\Promotion\Api\PromotionRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrderBuilder;
use Kemana\Promotion\Helper\Data;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class Promotion
 * @package Kemana\Promotion\Block\Promotion
 */
class Promotion extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var FilterGroup
     */
    protected $filterGroup;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var Loaded promotion
     */
    protected $currentPromotion = false;

    /**
     * @var Meta title
     */
    protected $metaTitle = '';

    /**
     * @var string Meta keywords
     */
    protected $metaKeyword = '';

    /**
     * @var string Meta description
     */
    protected $metaDescription = '';

    /**
     * @var Data
     */
    protected $promotionHelper;

    /**
     * Promotion constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param FilterBuilder $filterBuilder
     * @param PromotionRepositoryInterface $promotionRepository
     * @param StoreManagerInterface $storeManager
     * @param Filter $filter
     * @param FilterGroup $filterGroup
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Data $promotionHelper
     * @param FilterProvider $filterProvider
     * @param Context $context
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        PromotionRepositoryInterface $promotionRepository,
        StoreManagerInterface $storeManager,
        Filter $filter,
        FilterGroup $filterGroup,
        SortOrderBuilder $sortOrderBuilder,
        Data $promotionHelper,
        FilterProvider $filterProvider,
        Context $context
    )
    {
        $this->promotionRepository = $promotionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->storeManager = $storeManager;
        $this->filter = $filter;
        $this->filterGroup = $filterGroup;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->promotionHelper = $promotionHelper;
        $this->filterProvider = $filterProvider;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPromotions()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $limit = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : $this->promotionHelper->getPromotionPerPage();

        $filterStatus = $this->filterBuilder->setField('is_active')
            ->setValue(Data::ACTIVE_STATUS)
            ->setConditionType('eq')
            ->create();

        $filterGroupStatus = $this->filterGroupBuilder
            ->addFilter($filterStatus)
            ->create();

        $filterStores = $this->filterBuilder->setField('stores')
            ->setValue('%' . $this->storeManager->getStore()->getId() . '%')
            ->setConditionType('like')
            ->create();

        $filterAllStores = $this->filterBuilder->setField('stores')
            ->setValue(Data::ALL_STORE_ID)
            ->setConditionType('eq')
            ->create();

        $filterGroupStores = $this->filterGroupBuilder
            ->addFilter($filterStores)
            ->addFilter($filterAllStores)
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection('ASC')
            ->create();

        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $this->searchCriteriaBuilder->setFilterGroups([$filterGroupStatus, $filterGroupStores]);

        $this->searchCriteriaBuilder->setPageSize($limit);
        $this->searchCriteriaBuilder->setCurrentPage($page);

        return $this->promotionRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * @param null $promotionId
     * @return \Kemana\Promotion\Api\Data\PromotionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPromotionById($promotionId = null)
    {
        if (!$promotionId) {
            $promotionId = $this->_request->getPost('promotion_id');
        }

        try {
            if ($this->currentPromotion) {
                $promotion = $this->currentPromotion;
            } else {
                $promotion = $this->promotionRepository->getById($promotionId);
            }

            return $promotion;
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(__('Promotion with id "%1" does not exist. Error :'.$e->getMessage(), $promotionId));
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadMetaData() {
        $promotion = $this->getPromotionById();

        $metaInformation = [];

        $metaInformation['title'] = $promotion->getMetaTitle();
        $metaInformation['keywords'] = $promotion->getMetaKeywords();
        $metaInformation['description'] = $promotion->getMetaDescription();

        return $metaInformation;
    }

    /**
     * @return $this|Promotion
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->prepareCustomBreadcrumb();

        if ($this->_request->getFullActionName() != 'promotions_index_index') {
            return $this;
        }

        $promotionCollection = $this->getPromotions();

        $gridAllowedValues = $this->promotionHelper->getPromotionGridPagerAllowedValues();
        $pagerValues = [];

        foreach (explode(',',$gridAllowedValues) as $option) {
            $pagerValues[$option] = $option;
        }

        if ($promotionCollection['collection']) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'custom.history.pager')
                ->setAvailableLimit($pagerValues)
                ->setShowPerPage(true)->setCollection($promotionCollection['collection']);

            $this->setChild('pager', $pager);
            $promotionCollection['collection']->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Process content from rich editors
     *
     * @param $html
     * @return string
     * @throws \Exception
     */
    public function processContent($html)
    {
        $html = $this->filterProvider->getPageFilter()->filter($html);
        return $html;
    }

    /**
     * Get landing page promotion short content length
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPromotionShortContentLength() {
        return $this->promotionHelper->getPromotionShortContentLength();
    }

    /**
     * Get Medial Folder Url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaFolderUrl() {
        return $this->promotionHelper->getMediaFolderUrl();
    }

    /**
     * Get Base Url
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl() {
        return $this->promotionHelper->getBaseUrl();
    }

    /**
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareCustomBreadcrumb()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();

        if ($breadcrumbsBlock) {

            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go Back to Home'),
                    'link' => $baseUrl
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'promotion_landing',
                [
                    'label' => __('Promotions'),
                    'title' => __('Promotions'),
                    'link' => $baseUrl . $this->promotionHelper->getLandingPageFrontName()
                ]
            );

            if ($this->_request->getFullActionName() == 'promotions_index_detail') {

                $promotion = $this->getPromotionById();

                $breadcrumbsBlock->addCrumb(
                    'promotion_detail',
                    [
                        'label' => __($promotion->getTitle()),
                        'title' => __($promotion->getTitle()),
                        'link' => $baseUrl . $promotion->getIdentifier()
                    ]
                );
            }
        }
    }

}
