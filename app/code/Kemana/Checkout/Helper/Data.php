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
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Bundle\Model\Product\Type;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Inventory\Model\ResourceModel\SourceItem\Collection;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory;
use Magento\Inventory\Model\ResourceModel\StockSourceLink;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   
    /**
     * @var Configurable
     */
    private $configurableProduct;

    /**
     * @var StoreManagerInterface
     */
    private $storemanager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CollectionFactory
     */
    private $sourceItemCollection;

    /**
     * @param Configurable $configurableProduct
     * @param StoreManagerInterface $storemanager
     * @param Context $context
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $sourceItemCollection
     */
    public function __construct(
        Configurable          $configurableProduct,
        StoreManagerInterface $storemanager,
        Context               $context,
        ResourceConnection $resourceConnection,
        CollectionFactory $sourceItemCollection
    )
    {
        $this->configurableProduct = $configurableProduct;
        $this->storeManager =  $storemanager;
        $this->resourceConnection = $resourceConnection;
        $this->sourceItemCollection = $sourceItemCollection;

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
            return $this->getSourceStockAssignation($product);
        } elseif ($product->getTypeId() == Type::TYPE_CODE) {
            return $this->getBundledProductChildStockStatus($product);
        } else {
            return $this->getSourceStockAssignation($product);
        }
        
    }

    /**
     * get bundle product child stock status
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool 
     */
    public function getBundledProductChildStockStatus($product)
    {
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
                $isInStockBundleProductChilds[] = $this->getSourceStockAssignation($product, $bundle); // 0 = out of stock, 1 = is in stock
            }
        }
        if (count(array_unique($isInStockBundleProductChilds)) === 1) {// check if 1 bundle child items is out of stock
            $stockStatus = true;
        } else {
            $stockStatus = false;
        }

        return $stockStatus;
    }
    
    /**
     * Get Source Item collection
     *
     * @param $sku
     * @param $bundle
     * @return bool
     */
    public function getSourceStockAssignation($product, $bundle = null)
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $collection = $this->sourceItemCollection->create();
        $stockStatus = false;

        if (!empty($bundle)) {
            $sku = $bundle->getSku();
        } else {
            $sku = $product->getSku();
        }
        
        $isBackOrderEnable = 0;
        if ($getStockItem = $product->getExtensionAttributes()->getStockItem()) {
            $isBackOrderEnable = $getStockItem->getBackorders();
        }
        
        $checkStockSource = $this->stockFilterByWebsiteCodeAndSku($collection, $websiteCode, $sku, $isBackOrderEnable);
        
        if (!empty($checkStockSource->getData())) {
            $stockStatus = true;
        } else {
            $stockStatus = false;
        }

        return $stockStatus;
    }

    /**
     * Filters Source Stock Item collection by provided website code and sku
     *
     * @param Collection $collection
     * @param $websiteCode
     * @param $sku
     * @param $isBackOrderEnable
     * @return Collection
     */
    public function stockFilterByWebsiteCodeAndSku(Collection $collection, $websiteCode, $sku, $isBackOrderEnable): Collection
    {
        $select = $collection->getSelect();
        $select->joinLeft(
            ['source_stock_link' => $this->resourceConnection
                ->getTableName(StockSourceLink::TABLE_NAME_STOCK_SOURCE_LINK)],
            'main_table.source_code = source_stock_link.source_code',
            ['stock_id']
        )->joinLeft(
            ['sales_channel'=> $this->resourceConnection->getTableName('inventory_stock_sales_channel')],
            'sales_channel.stock_id = source_stock_link.stock_id',
            ['code']
        )->where('sales_channel.code =?', $websiteCode
        )->where('main_table.sku =?', $sku
        )->where('main_table.status != 0');
        
        if (!$isBackOrderEnable) {
            $select->where('main_table.quantity > 0');
        }
        return $collection;
    }
}
