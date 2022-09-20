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
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
    )
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->stockRegistry = $stockRegistry;
        $this->sourceItem = $sourceItem;
        $this->sourceItemSave = $sourceItemSave;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @return false|mixed
     */

    public function getUnSyncInventorysFromErp($apiFunction, $soapAction, $inventoryData)
    {
        $postParameters = $inventoryData;

        $getInventoryFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToGetUnSyncInventorysFromApi($apiFunction, $soapAction, $postParameters));

        if (isset($getInventoryFromErp['response'])) {
            return $getInventoryFromErp;
        }

        return false;
    }

    /**
     * @param $productSkus
     * @return array
     */
    public function inventoryApiCall($productSkus){
            $productSkus = array_unique($productSkus);
            $productSkus = implode("|", $productSkus);
            $dataToGetStock = [
                "Field" => "ProductNo",
                "Criteria" => $productSkus,
            ];
        $this->helper->inventorylog("Request: ".json_encode($dataToGetStock), 'info');

        $dataToGetStock = $this->helper->convertArrayToXml($dataToGetStock);
        $response = $this->getUnSyncInventorysFromErp($this->helper->getFunctionInventoryStock(),
            $this->helper->getSoapActionGetInventoryStock(), $dataToGetStock);

        $this->helper->inventorylog("Response: ".json_encode($response), 'info');
        
        if(isset($response['response']) && isset($response['response']['ProductNo'])){
            $this->updateStock($response['response']['ProductNo'],$response['response']['Inventory']);
        }elseif(isset($response['response']) && is_array($response['response'])){
            foreach ($response['response'] as $key => $product) {
                if(isset($product['ProductNo']) && isset($product['Inventory'])){
                    $this->updateStock($product['ProductNo'],$product['Inventory']);
                }
            }
        }else{
            $this->helper->inventorylog('0 Product stock update');
        }
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
            $message = "Updated inventory sku: ".$sku ." and stock: " .$qty;
            $this->helper->inventorylog($message, 'info');
        }catch(Exception $e){
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
