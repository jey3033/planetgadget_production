<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\MsDynamics\Cron;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Store\Model\Store;

/**
 * Class SyncProductsFromErp
 */
class SyncProductsFromErp
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Product
     */
    protected $erpProduct;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    protected $categoryLinkRepository;

    /**
     * @param \Kemana\MsDynamics\Helper\Data                      
     * @param \Kemana\MsDynamics\Model\Api\Erp\Product           
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory   
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface 
     * @param \Magento\Framework\App\State                         
     * @param \Magento\Catalog\Model\CategoryFactory               
     * @param \Magento\Catalog\Api\CategoryLinkManagementInterface 
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                      $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Product            $erpProduct,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory   $productFactory, 
        \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\App\State                         $state,
        \Magento\Catalog\Model\CategoryFactory               $categoryFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkRepository,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory           $inventory
    )
    {
        $this->helper = $helper;
        $this->erpProduct = $erpProduct;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->categoryFactory = $categoryFactory;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->inventory = $inventory;
    }

    /**
     * @throws InputMismatchException
     * @throws InputException
     * @throws LocalizedException
     */
    public function syncProductsFromErpToMagento()
    {   
        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Started to get the not synced product from ERP and then create product in Magento using Cron Job', 'info');

        $dataToGetProducts = [
            "Field" => "Synced",
            "Criteria" => "false"
        ];

        $dataToGetProducts = $this->helper->convertArrayToXml($dataToGetProducts);


        $getProductsFromErp = $this->erpProduct->getUnSyncProductsFromErp($this->helper->getFunctionProductList(),
            $this->helper->getSoapActionGetProductList(), $dataToGetProducts);

        if (!is_array($getProductsFromErp) || !count($getProductsFromErp)) {
            $this->helper->log('No product received from ERP to create product in Magento', 'error');
            return;
        }
        if(!$this->state->getAreaCode()){
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }
        $productsFromErp = array_chunk($getProductsFromErp['response'],50);
        $arrayCount = 0;
        foreach ($productsFromErp as $arraykey => $productsArray) {
            $arrayCount = $arraykey + 1;
            $this->helper->log($arrayCount.'th 50 product createing.....', 'info');
            $ackProductData = [];
            $count = 0;
            foreach ($productsArray as $key => $productdata) {
                if($count > 5 && !$this->helper->isApiMode()){
                    break;
                }
                $count ++;
                if(isset($productdata['ProductNo']) && $productdata['ProductNo']){
                    try {
                        $this->helper->log('Started to create the product in Magento for ERP Product : ' . $productdata['ProductNo'], 'info');

                        // validataion for product api json
                        if(!array_key_exists("ProductNo",$productdata) || !array_key_exists("GrossWeight",$productdata) || !array_key_exists("Price",$productdata) || !array_key_exists("ItemCategory",$productdata)){
                            $this->helper->log('Invalid parameter for this product : ' . $productdata['ProductNo'], 'error');
                            continue;
                        }
                        
                        $name = isset($productdata['Description']) ? $productdata['Description'] : $productdata['ProductNo'];
                        $weight = ($productdata['GrossWeight'] > 0) ? $productdata['GrossWeight'] : 0.5;
                        $product = $this->productFactory->create();
                        $product->setSku($productdata['ProductNo']);
                        $product->setName($name);
                        $product->setWeight($weight);
                        $product->setPrice($productdata['Price']);
                        $product->setAttributeSetId(4);
                        $product->setTypeId(Type::TYPE_SIMPLE);
                        $product->setData('store_id', Store::DEFAULT_STORE_ID);
                        $product->setStatus(Status::STATUS_ENABLED);
                        $product = $this->productRepository->save($product);
                        $categoryIds = [];
                        $categoryCollection = $this->categoryFactory->create()->getCollection()
                                ->addAttributeToFilter('name', array('in' => array(
                                    $productdata['ItemCategory'], 
                                    ucfirst($productdata['ItemCategory']),
                                    strtoupper($productdata['ItemCategory']),
                                    strtolower($productdata['ItemCategory'])
                                )))->getFirstItem();
                        $catId = !empty($categoryCollection) ? $categoryCollection->getId() : 0;   
                        if($catId){
                            $categoryIds[] = $catId;
                        }
                        if(!empty($categoryIds)){
                            $this->categoryLinkRepository->assignProductToCategories($product->getSku(), $categoryIds);
                        }

                        if($product->getId()){
                            $this->inventory->inventoryApiCall([$product->getSku()]);
                            $this->helper->log('Successfully created the product in Magento for ERP product : ' . $productdata['ProductNo'], 'info');

                            $ackProductData[] = [
                                            "ProductNo" => $productdata['ProductNo'],
                                            "MagentoProductID" => $product->getId(),
                                        ];
                        }
                    } catch (Exception $e) {
                        $this->helper->log('Unable to create the product for EPR product ' . $productdata['ProductNo'] . ' in Magento. Error : ' . $e->getMessage(), 'error');
                    }
                }
            }

            // Ack call

            if (empty($ackProductData)) {
                return;
            }

            $this->helper->log('Start Ack call for products by CRON', 'info');

            $ackProductData = $this->helper->convertAckProductListToXml($ackProductData);

            $ackProduct = $this->erpProduct->ackProduct($this->helper->getFunctionAckProduct(),
                $this->helper->getSoapActionAckProduct(), $ackProductData);

            if ($ackProduct['responseStatus'] == '100') {
                $this->helper->log('Ack call successfully done for below product' . $ackProductData, 'info');
                $this->helper->log('End to get the not synced products from ERP and then create in Magento using Cron Job', 'info');
                echo $ackProductData;
            }
        }
        return true;
    }

}
