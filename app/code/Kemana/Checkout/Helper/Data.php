<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Checkout
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Checkout\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Bundle\Model\Product\Type;
use Magento\Store\Model\StoreManagerInterface;
/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StockRegistryInterface|null
     */
    private $stockRegistry;

    /**
     * @var Configurable
     */
    private $configurableProduct;

    /**
     * @var StoreManagerInterface
     */
    private $storemanager;
    /**
     * @param StockRegistryInterface $stockRegistry
     * @param Configurable $configurableProduct
     * @param StoreManagerInterface $storemanager
     * @param Context $context
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        Configurable          $configurableProduct,
        StoreManagerInterface $storemanager,
        Context               $context
    )
    {
        $this->stockRegistry = $stockRegistry;
        $this->configurableProduct = $configurableProduct;
        $this->storeManager =  $storemanager;

        parent::__construct($context);
    }

    /**
     * get stock status
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param $attribOptions
     * @return bool 
     */
    public function getStockStatus($product, $attribOptions)
    {
       
        if($product->getTypeId() == Configurable::TYPE_CODE){
            
            $productChildId = $this->getConfigurableProductChildIdsByAttrValue($product, $attribOptions);
            $getProductChildStockStatus = $this->getConfigurableProductChildStockStatus($product->getId());
            
            return $getProductChildStockStatus[$productChildId] ? true : false;
            
        }else if($product->getTypeId() == Type::TYPE_CODE){ // bundle products
            
            return $this->getBundledProductChildStockStatus($product);
            
        }else{
            /** @var StockItemInterface $stockItem */
            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            $isInStock = $stockItem ? $stockItem->getIsInStock() : false;
            return $isInStock;
        }
    }
    /**
     * get stock status product child
     *
     * @param int $productId
     * @return array 
     */
    public function getConfigurableProductChildStockStatus($productId)
    {
        $data = [];
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct */
        $children = $this->configurableProduct->getChildrenIds($productId);
        foreach ($children as $child) {
            foreach ($child as $item) {
                /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
                $stockItem = $this->stockRegistry->getStockItem($item);
                if($stockItem) {
                    $data[$item] = $stockItem->getData('is_in_stock');
                }
            }
        }

        return $data;
    }
    /**
     * get configurable product child ids by attribute
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $attribOptions ex. [ color => Red ]
     * @return int 
     */
    public function getConfigurableProductChildIdsByAttrValue($product, $attribOptions){
        $storeId = $this->storeManager->getStore()->getStoreId();
        
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($storeId, $product);
        $usedProducts = $productTypeInstance->getUsedProducts($product);
        
        foreach($attribOptions as $option){
            
            $attr_code = strtolower($option['label']);
            $attr_value = $option['value'];
            
            foreach ($usedProducts  as $child) {
        
                $customValue = $child->getAttributeText($attr_code);
                if($customValue == $attr_value){
                    return $child->getId();
                }
        
            }
        }
    }

    /**
     * get bundle product child ids by attribute
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool 
     */
    public function getBundledProductChildStockStatus($product){
        $data = [];
        $bundleIds = [];
        $stockStatus = true;
       
        $bundleIds = array_reduce(
            $product->getTypeInstance(true)->getChildrenIds($product->getId()),
            function (array $reduce, $value) {
                return array_merge($reduce, $value);
            }, []);

        foreach($bundleIds as $item){
            $stockItem = $this->stockRegistry->getStockItem($item);
            $test = $stockItem->getBackorders();
            if($stockItem) {
                $data[$item] = $stockItem->getData('is_in_stock');
            }
        }
        if(array_search("0",$data))//
        {
            $stockStatus = false;
        }
        return $stockStatus;
    }

        
}
