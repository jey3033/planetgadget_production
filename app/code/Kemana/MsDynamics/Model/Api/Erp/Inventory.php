<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api\Erp;

/**
 * Class Inventory
 */
class Inventory
{
    /**
     * @var \Kemana\MsDynamics\Model\Api\Request
     */
    protected $request;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\MsDynamics\Model\Api\Request $request
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        \Kemana\MsDynamics\Model\Api\Request $request,
        \Kemana\MsDynamics\Helper\Data       $helper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\InventoryApi\Api\Data\SourceItemInterface $sourceItem,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    )
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->stockRegistry = $stockRegistry;
        $this->sourceItem = $sourceItem;
        $this->sourceItemSave = $sourceItemSave;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @return false|mixed
     */

    public function getUnSyncInventorysFromErp($apiFunction, $soapAction, $inventoryData)
    {
        $postParameters = $inventoryData;
        return $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToGetUnSyncInventorysFromApi($apiFunction, $soapAction, $postParameters));
        
    }

    /**
     * @param $productSkus
     * @return array
     */
    public function inventoryApiCall($productSkus){
            $productSkusArray = array_unique($productSkus);
            $productSkus = implode("|", $productSkusArray);

            $storeSources = $this->getSources();   
            $sources = implode("|", $storeSources);

            $dataToGetStock = [
                "productFilter" => $productSkus,
                "locationFilter" => $sources
            ];
        $this->helper->inventorylog("Request: ".json_encode($dataToGetStock), 'info');

        $dataToGetStock = $this->helper->convertArrayToXml($dataToGetStock);
        $response = $this->getUnSyncInventorysFromErp($this->helper->getFunctionInventoryStock(),
            $this->helper->getSoapActionGetInventoryStock(), $dataToGetStock);

        $this->helper->inventorylog("Response: ".json_encode($response), 'info');

        $totalStock = [];
        $inventoryData = [];
        if(isset($response['response'])){
            $inventorysources = explode(";",$response['response']);
            foreach ($inventorysources as $key => $inventorysource) {
                $stockarray = explode(",",$inventorysource);
                if(isset($stockarray[0]) && isset($stockarray[1]) && isset($stockarray[2])){
                    if(isset($totalStock[$stockarray[0]])){
                        $totalStock[$stockarray[0]] = $totalStock[$stockarray[0]] + $stockarray[2];
                    }else{
                        $totalStock[$stockarray[0]] = $stockarray[2];
                    }
                    $inventoryData[$stockarray[0]][$stockarray[1]] = $stockarray[2];
                }
            }
        }else{
            $this->helper->inventorylog('No response.');
        }
        foreach ($productSkusArray as $sku) {
            foreach ($storeSources as $sources) {
                $qty = isset($inventoryData[$sku][$sources]) ? $inventoryData[$sku][$sources] : 0;
                $this->updateStock($sku,$qty,$sources);   
            }
        }
        $response['totalStock'] = $totalStock;
        return $response;
    }

    public function getSources(){
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('enabled', 1)->create();
        $sourceData = $this->sourceRepository->getList($searchCriteria);
        $sourceData = $sourceData->getItems();
        $sources = [];
        foreach ($sourceData as $key => $source) {
            array_push($sources,$source['source_code']);
        }
        return array_unique($sources);
    }
    /**
     * @param $sku
     * @param $qty
     * @param $sourceCode
     * @return array
     */
    public function updateStock($sku, $qty, $sourceCode = 'default'){
        try{
            $status = $qty > 0 ? 1 : 0;
            $this->sourceItem->setSku($sku);
            $this->sourceItem->setSourceCode($sourceCode);
            $this->sourceItem->setQuantity($qty);
            $this->sourceItem->setStatus($status);    
            $this->sourceItemSave->execute([$this->sourceItem]);

            $message = "Updated inventory sku: ".$sku ." source: ".$sourceCode." and stock: " .$qty;
            $this->helper->inventorylog($message, 'info');
        }catch(Exception $e){
            $this->helper->inventorylog($e->getMessage(), 'error');
        }
    }
}
