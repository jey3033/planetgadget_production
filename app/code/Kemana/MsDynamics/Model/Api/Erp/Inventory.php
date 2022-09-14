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
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    )
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @return false|mixed
     */
    public function ackInventory($apiFunction, $soapAction, $inventoryData)
    {
        $postParameters = $inventoryData;

        $getInventoryFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpAckListOfInventorys($apiFunction, $soapAction, $postParameters));

        if (isset($getInventoryFromErp['response'])) {
            return $getInventoryFromErp;
        }

        return false;
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
     * @param $productData
     * @return array
     */
    public function inventoryApiCall($productData){

        $dataToGetStock = [];
        
        foreach ($productData as $key => $product) {
            $dataToGetStock[] = [
                "Field" => "ProductNo",
                "Criteria" => $product,
            ];
        }

        $dataToGetStock[] = [
            "Field" => "LocationFilter",
            "Criteria" => "PG-5",
        ];

        $dataToGetStock = $this->helper->convertInventoryArrayToXml($dataToGetStock);
        $getProductsFromErp = $this->getUnSyncInventorysFromErp($this->helper->getFunctionInventoryStock(),
            $this->helper->getSoapActionGetInventoryStock(), $dataToGetStock);

        return $getProductsFromErp;

    }

    /**
     * @param $sku
     * @param $qty
     * @return array
     */
    public function updateStock($sku,$qty){
        try{
            $stockItem = $this->stockRegistry->getStockItemBySku($sku);
            $stockItem->setQty($qty);
            $this->stockRegistry->updateStockItemBySku($sku, $stockItem);
        }catch(Exception $e){
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }
}
