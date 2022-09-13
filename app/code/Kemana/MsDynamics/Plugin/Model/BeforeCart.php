<?php

namespace Kemana\MsDynamics\Plugin\Model;

use Magento\Checkout\Model\Cart;

class BeforeCart
{   
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     * @param \Magento\Catalog\Model\ProductRepository   $productRepository
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Catalog\Model\ProductRepository          $productRepository
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->_productRepository = $productRepository;
    }

    public function beforeAddProduct(Cart $subject, $productInfo, $requestInfo = null)
    {
        $productsku = $productInfo->getSku();
        if($productsku){
            $this->helper->inventorylog('Add To Cart API call: ' . $productsku, 'info');
            $productdata = [];
            array_push($productdata, $productsku);
            if(!empty($productdata)){
                $this->erpInventory->inventoryApiCall($productdata);
            }
        }
        return [$productInfo,$requestInfo];
    }
}