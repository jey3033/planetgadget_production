<?php

namespace Kemana\Common\Plugin\Block\Catalog\Product;

use Magento\Catalog\Model\Config;

/**
 * Class ProductsList
 */
class ProductsList
{
    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @param Config $catalogConfig
     */
    public function __construct(Config $catalogConfig)
    {
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * @param \Kemana\AcceleratorBase\Block\Catalog\Product\ProductsList $subject
     * @param $collection
     * @return mixed
     */
    public function afterGetCustomProductsCollection(\Kemana\AcceleratorBase\Block\Catalog\Product\ProductsList $subject, $collection) {
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addUrlRewrite();

        return $collection;
    }

}
