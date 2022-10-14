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

namespace Kemana\MsDynamics\Observer\Inventory;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class BeforeAddToCart
 */
class BeforeAddToCart implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->configurable = $configurable;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnable()) {
            return;                                             
        }
        $addProduct = $observer->getEvent()->getProduct();
        $requestInfo = $observer->getEvent()->getInfo();
        $productSkus = [];

        // simple product logic
        if($addProduct->getTypeId() == 'simple'){
            $productSkus[] = $addProduct->getSku();
        }

        // configuration product logic
        if ($addProduct->getTypeId() == Configurable::TYPE_CODE) 
        {
            $attributes = $requestInfo['super_attribute'];
            $simple_product = $this->configurable->getProductByAttributes($attributes, $addProduct);
            if($simple_product){
                $productSkus[] = $simple_product->getSku();
            }
        }

        // bundle product logic
        if($addProduct->getTypeId() == 'bundle'){
            $buyRequest = new \Magento\Framework\DataObject($requestInfo);
            $cartCandidates = $addProduct->getTypeInstance()->prepareForCartAdvanced($buyRequest, $addProduct);
            foreach ($cartCandidates as $cartCandidate) {
                if ($cartCandidate->getTypeId() != 'bundle') {
                        $productSkus[] = $cartCandidate->getSku();
                }
            }
        }

        $this->helper->inventorylog('Before Add To Cart API call: ' . json_encode($productSkus), 'info');
        if(!empty($productSkus)){
            $this->erpInventory->inventoryApiCall($productSkus);
        }
    }
}
