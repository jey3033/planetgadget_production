<?php

namespace Kemana\SourceDistanceShipping\Model\Source;


class SourceLocation
{
    protected $searchCriteriaBuilder;
    protected $sourceItemRepositoryInterface;
    protected $sourceItemRepository;
    protected $sourceRepositoryInterface;
    protected $helper;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryInterface,
        \Magento\Inventory\Model\SourceItemRepository $sourceItemRepository,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface,
        \Kemana\SourceDistanceShipping\Helper\Data $helper
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepositoryInterface = $sourceItemRepositoryInterface;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
        $this->helper = $helper;

    }

    public function sourceLocationsToFullFillOrder($orderItemData)
    {

        foreach ($orderItemData as $itemData) {
            $this->searchCriteriaBuilder->addFilter('sku', $itemData['sku']);
            $this->searchCriteriaBuilder->addFilter('quantity', $itemData['qty'], 'gt');
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();

        $sourceItemData = $this->sourceItemRepositoryInterface->getList($searchCriteria);
        return $sourceItemData->getItems();
    }

    public function getSourceLocationDetails($sourceCode)
    {
        try {
            return $this->sourceRepositoryInterface->get($sourceCode);
        } catch (\Exception $e) {
            $this->helper->log($e->getMessage(),'error');
        }
    }

}
