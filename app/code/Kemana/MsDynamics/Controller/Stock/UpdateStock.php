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


namespace Kemana\MsDynamics\Controller\Stock;

use Magento\Framework\Controller\Result\JsonFactory;
use Kemana\StockAvailabilityPopup\Model\Stock\SourceDataForSku;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class UpdateStock
 */
class UpdateStock extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Inventory
     */
    protected $erpInventory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @var \Magento\Bundle\Api\ProductLinkManagementInterface
     */
    protected $productLinkManagement;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagement       
     */
    public function __construct(
        \Magento\Framework\App\Action\Context             $context,
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Framework\App\Request\Http               $request,
        \Magento\Catalog\Model\ProductRepository          $productRepository,
        \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagement,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData  $configurableAttributeData
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->request = $request;
        $this->_productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configurableAttributeData = $configurableAttributeData;
        return parent::__construct($context);
    }


    /**
     * Get Options for Configurable Product Options
     *
     * @param Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];
        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }

    /**
     * Get allowed attributes
     *
     * @param Product $product
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return ($product->getTypeId() == Configurable::TYPE_CODE)
            ? $product->getTypeInstance()->getConfigurableAttributes($product)
            : [];
    }

    public function execute()
    {
        $productId = (int) $this->request->getParam('id');
        $resultJson = $this->resultJsonFactory->create();

        if (!$this->helper->isEnable()) {
            $response = ["msDynamics" => false];
            return $resultJson->setData($response);                                    
        }

        if($productId){
            $product = $this->_productRepository->getById($productId);
            
            $productdata = [];

            if($product->getTypeId() == 'simple'){
                array_push($productdata, $product->getSku());
            }

            // configuration product logic
            $childProduct = [];
            if ($product->getTypeId() == "configurable") 
            {
                $_children = $product->getTypeInstance()->getUsedProducts($product);
                foreach ($_children as $child){
                    $childProduct[$child->getSku()] = $child->getId();
                    array_push($productdata, $child->getSku());        
                }    
            }
            
            // bundle product logic
            if($product->getTypeId() == 'bundle'){
                $items = $this->productLinkManagement->getChildren($product->getSku());
                foreach ($items as $item) {
                   array_push($productdata, $item->getSku());         
                }
            }

            // grouped product logic
            if($product->getTypeId() == 'grouped'){
                $_children = $product->getTypeInstance(true)->getAssociatedProducts($product);
                foreach ($_children as $child) {
                    array_push($productdata, $child->getSku());
                }
            }            
            
            $this->helper->inventorylog('PDP page API call: ' . json_encode($productdata), 'info');
            if(!empty($productdata)){
                $instockproductids = [];
                $response = $this->erpInventory->inventoryApiCall($productdata);
                    
                if ($product->getTypeId() == "configurable") 
                {
                    if($response['curlStatus'] != 500 && isset($response['totalStock']) && count($response['totalStock']) > 0){
                        foreach ($response['totalStock'] as $sku => $qty) {
                            if($qty > 0){
                                array_push($instockproductids,$childProduct[$sku]);
                            }
                        }
                        $options = $this->getOptions($product, $_children);
                        $attributesData = $this->configurableAttributeData->getAttributesData($product, $options);
                        if(isset($attributesData['attributes'])){
                            foreach ($attributesData['attributes'] as $keyattributes => $attributes) {
                                foreach ($attributes['options'] as $productoptionkey => $productoption) {
                                    $tem_instock = [];
                                    foreach ($productoption['products'] as $key => $optionproduct) {
                                        if(in_array($optionproduct, $instockproductids)){
                                            array_push($tem_instock,$optionproduct);
                                        }
                                    }
                                    $attributesData['attributes'][$keyattributes]['options'][$productoptionkey]['products'] = $tem_instock;
                                }                                                            
                            }
                        }else{
                            $attributesData['attributes'] = [];
                        }
                        return $resultJson->setData([
                                                     "msDynamics" => true,
                                                     "apiresponse" => $response,
                                                     "attributes"  => $attributesData['attributes'],
                                                     "instock"     => count($instockproductids) > 0 ? true : false
                                                ]);    
                    }else{
                        return $resultJson->setData([
                                                     "msDynamics" => true,
                                                     "apiresponse" => $response,
                                                     "attributes"  => [],
                                                     "instock"     => $product->isSalable()
                                                ]);    
                    }
                }
                
                return $resultJson->setData([
                                             "msDynamics" => true,
                                             "apiresponse" => $response,
                                             "instock"     => $product->isSalable()
                                        ]);
            }
        }
    }
}
