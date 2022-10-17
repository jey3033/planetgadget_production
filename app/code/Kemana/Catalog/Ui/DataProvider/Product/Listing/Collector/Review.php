<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\Catalog\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderExtensionFactory;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;

class Review implements ProductRenderCollectorInterface
{
    /** REVIEW html key */
    const KEY = "review";

    /**
     * @var ProductRenderExtensionFactory
     */
    private $productRenderExtensionFactory;
    protected $_reviewFactory;
    protected $_ratingFactory;
    protected $_storeManager;
    /**
     * Review constructor.
     * @param ProductRenderExtensionFactory $productRenderExtensionFactory
     */
    public function __construct(
        ProductRenderExtensionFactory $productRenderExtensionFactory,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Rating $ratingFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    
    ) {
        $this->productRenderExtensionFactory = $productRenderExtensionFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        $extensionAttributes = $productRender->getExtensionAttributes();

        if (!$extensionAttributes) {
            $extensionAttributes = $this->productRenderExtensionFactory->create();
        }

        if($product->getSku())
        {
            $product_id = $product->getId();

            $_ratingSummary = $this->_ratingFactory->getEntitySummary($product_id);

            $ratingCollection = $this->_reviewFactory->create()->getResourceCollection()->addStoreFilter($this->_storeManager->getStore()->getId())->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->addEntityFilter('product', $product_id);

            $review_count = count($ratingCollection); // How many review in that specific product

            if($review_count > 0){
                $product_rating = $_ratingSummary->getSum() / $_ratingSummary->getCount();  // Product rating in percentage

                $extensionAttributes
                    ->setReview($product_rating);
            } 
        }

        $productRender->setExtensionAttributes($extensionAttributes);
    }
}