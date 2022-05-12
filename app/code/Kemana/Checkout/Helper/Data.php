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
     * check if item is in stock or out of stock
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool 
     */
    public function getStockStatus($product)
    {
        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            return $this->stockRegistry->getStockItemBySku($product->getSku())->getIsInStock();
        } elseif ($product->getTypeId() == Type::TYPE_CODE) {
            return $this->getBundledProductChildStockStatus($product);
        } else {
            return $this->stockRegistry->getStockItemBySku($product->getSku())->getIsInStock();
        }
        
    }

    /**
     * get bundle product child stock status
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool 
     */
    public function getBundledProductChildStockStatus($product){
        
        $isInStockBundleProductChilds = [];
        $stockStatus = true;
        //get all the selection products used in bundle product.
        $allBundleproductSelection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
        
        $OrigBundleSKus = $product->getSku();
        
        foreach ($allBundleproductSelection as $bundle) {
            if (str_contains($OrigBundleSKus, $bundle->getSku())) { 
                $isInStockBundleProductChilds[] = (int) $this->stockRegistry->getStockItemBySku($bundle->getSku())->getIsInStock(); // 0 = out of stock, 1 = is in stock
            }

        }

        if (count(array_unique($isInStockBundleProductChilds)) === 1) {// check if 1 bundle child items is out of stock
            $stockStatus = true;
        } else {
            $stockStatus = false;
        }

        return $stockStatus;
    }
    
}
