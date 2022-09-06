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
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

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
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer           
     * @param \Magento\Catalog\Api\Data\ProductInterfaceFactory   
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface 
     * @param \Magento\Framework\App\State                         
     * @param \Magento\Catalog\Model\CategoryFactory               
     * @param \Magento\Catalog\Api\CategoryLinkManagementInterface 
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                      $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer           $erpCustomer,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory   $productFactory, 
        \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\App\State                         $state,
        \Magento\Catalog\Model\CategoryFactory               $categoryFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkRepository
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->categoryFactory = $categoryFactory;
        $this->categoryLinkRepository = $categoryLinkRepository;
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


        $getProductsFromErp = $this->erpCustomer->getUnSyncCustomersFromErp($this->helper->getFunctionProductList(),
            $this->helper->getSoapActionGetProductList(), $dataToGetProducts);

        if (!is_array($getProductsFromErp) || !count($getProductsFromErp)) {
            $this->helper->log('No product received from ERP to create product in Magento', 'error');
            return;
        }

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $ackProductData = [];

        foreach ($getProductsFromErp['response'] as $key => $productdata) {
            if(isset($productdata['ProductNo']) && $productdata['ProductNo']){
                try {
                    $this->helper->log('Started to create the product in Magento for ERP Product : ' . $productdata['ProductNo'], 'info');

                    $product = $this->productFactory->create();
                    $product->setSku($productdata['ProductNo']);
                    $product->setName($productdata['Description']);
                    $product->setWeight($productdata['GrossWeight']);
                    $product->setPrice($productdata['Price']);
                    $product->setAttributeSetId(4);
                    $product->setTypeId(Type::TYPE_SIMPLE);
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

        $this->helper->log('Start Ack call for customers by CRON', 'info');

        $ackProductData = $this->helper->convertAckProductListToXml($ackProductData);

        $ackProduct = $this->erpCustomer->ackCustomer($this->helper->getFunctionAckProduct(),
            $this->helper->getSoapActionAckProduct(), $ackProductData);

        if ($ackProduct['responseStatus'] == '100') {
            $this->helper->log('Ack call successfully done for below product' . $ackProductData, 'info');
            $this->helper->log('End to get the not synced products from ERP and then create in Magento using Cron Job', 'info');
            return $ackProductData;
        }
    }

}
