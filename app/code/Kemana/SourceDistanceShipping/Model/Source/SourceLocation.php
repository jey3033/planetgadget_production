<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SourceDistanceShipping
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SourceDistanceShipping\Model\Source;

/**
 * Class SourceLocation
 */
class SourceLocation
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemRepositoryInterface
     */
    protected $sourceItemRepositoryInterface;

    /**
     * @var \Magento\Inventory\Model\SourceItemRepository
     */
    protected $sourceItemRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    protected $sourceRepositoryInterface;

    /**
     * @var \Kemana\SourceDistanceShipping\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\SourceItemRepositoryInterface $sourceItemRepositoryInterface
     * @param \Magento\Inventory\Model\SourceItemRepository $sourceItemRepository
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepositoryInterface
     * @param \Kemana\SourceDistanceShipping\Helper\Data $helper
     */
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

    /**
     * Search the Source Repository to get the source locations which can fulfill all the supplied order items and qtys
     *
     * @param $orderItemData
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function sourceLocationsToFullFillOrder($orderItemData)
    {
        $sourcesForItems = [];
        $count = 0;

        foreach ($orderItemData as $itemData) {
            $this->searchCriteriaBuilder->addFilter('sku', $itemData['sku']);
            $this->searchCriteriaBuilder->addFilter('quantity', $itemData['qty'], 'gt');
            $this->searchCriteriaBuilder->addFilter('status', '1');
            $this->searchCriteriaBuilder->addFilter('source_code', 'default', 'neq');

            $searchCriteria = $this->searchCriteriaBuilder->create();
            $sourceItemData = $this->sourceItemRepositoryInterface->getList($searchCriteria);

            $sourceLocations = $sourceItemData->getItems();
             if (!empty($sourceLocations)) {
                 foreach ($sourceLocations as $sourceLocation) {
                     $sourcesForItems[$count][] = $sourceLocation->getSourceCode();
                 }

                 $count++;
            }
        }

        if (count($orderItemData) > 1) {

            $locationsIncludesAllItems = array_intersect(...$sourcesForItems);

            if (count($locationsIncludesAllItems)) {
                return $locationsIncludesAllItems;
            }

        } else {
            return $sourcesForItems;
        }

        return [];

    }

    /**
     * Get Source Location full details by the code
     *
     * @param $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceInterface|void
     */
    public function getSourceLocationDetails($sourceCode)
    {
        try {
            return $this->sourceRepositoryInterface->get($sourceCode);
        } catch (\Exception $e) {
            $this->helper->log($e->getMessage(),'error');
        }
    }

    /**
     * Get Source Locations address as an array
     *
     * @param $sourceLocations
     * @return array
     */
    public function getSourceLocationsAddress($sourceLocations): array
    {
        $sourceLocationsWithAddress = [];
        foreach ($sourceLocations as $sourceData) {
            $sourceLocationData = $this->getSourceLocationDetails($sourceData[0]);
            $sourceLocationsWithAddress[] = [
                'source_code' => $sourceLocationData->getSourceCode(),
                'address' => $this->helper->prepareAddressString($sourceLocationData)
            ];
        }

        return $sourceLocationsWithAddress;
    }

}
