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

namespace Kemana\Promotion\Model;

use Kemana\Promotion\Api\PromotionRepositoryInterface;
use Kemana\Promotion\Model\ResourceModel\Promotion as ResourcePromotion;
use Kemana\Promotion\Api\Data;
use Kemana\Promotion\Api\Data\PromotionInterface;
use Kemana\Promotion\Model\ResourceModel\Promotion\CollectionFactory as PromotionCollectionFactory;
use Kemana\Promotion\Api\Data\PromotionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class PromotionRepository
 *
 * @package Kemana\Promotion\Model
 */
class PromotionRepository implements PromotionRepositoryInterface
{

    /**
     * @var CollectionProcessorInterface
     */
    public $collectionProcessor;

    /**
     * @var SearchCriteriaInterface
     */
    public $searchCriteria;

    /**
     * @var ResourcePromotion
     */
    public $resource;

    /**
     * @var \Kemana\Promotion\Model\PromotionFactory
     */
    public $promotionFactory;

    /**
     * @var PromotionCollectionFactory
     */
    public $promotionCollectionFactory;

    /**
     * @var Data\PromotionSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var PromotionInterfaceFactory
     */
    public $dataPromotionFactory;


    /**
     * PromotionRepository constructor.
     *
     * @param ResourcePromotion $resource
     * @param PromotionFactory $promotionFactory
     * @param PromotionInterfaceFactory $dataPromotionFactory
     * @param PromotionCollectionFactory $promotionCollectionFactory
     * @param Data\PromotionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function __construct(
        ResourcePromotion $resource,
        PromotionFactory $promotionFactory,
        PromotionInterfaceFactory $dataPromotionFactory,
        PromotionCollectionFactory $promotionCollectionFactory,
        Data\PromotionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaInterface $searchCriteria
    )
    {
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteria = $searchCriteria;
        $this->resource = $resource;
        $this->promotionFactory = $promotionFactory;
        $this->promotionCollectionFactory = $promotionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPromotionFactory = $dataPromotionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @param PromotionInterface $promotion
     * @return PromotionInterface
     * @throws CouldNotSaveException
     */
    public function save(\Kemana\Promotion\Api\Data\PromotionInterface $promotion)
    {
        try {
            $this->resource->save($promotion);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $promotion;
    }

    /**
     * @param int $promotionId
     * @return PromotionInterface|Promotion
     * @throws NoSuchEntityException
     */
    public function getById($promotionId)
    {
        $promotion = $this->promotionFactory->create();
        $this->resource->load($promotion, $promotionId);
        if (!$promotion->getId()) {
            throw new NoSuchEntityException(__('Promotion with id "%1" does not exist.', $promotionId));
        }
        return $promotion;
    }


    /**
     * @param PromotionInterface $promotion
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Kemana\Promotion\Api\Data\PromotionInterface $promotion)
    {
        try {
            $this->resource->delete($promotion);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param int $promotionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($promotionId)
    {
        return $this->delete($this->getById($promotionId));
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\PromotionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->promotionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return [
            'searchResult' => $searchResults,
            'collection' => $collection
        ];
    }
}
