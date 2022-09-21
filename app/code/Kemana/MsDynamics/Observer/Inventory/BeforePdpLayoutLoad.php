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

/**
 * Class BeforePdpLayoutLoad
 */
class BeforePdpLayoutLoad implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Bundle\Api\ProductLinkManagementInterface
     */
    protected $productLinkManagement;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagement       
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Framework\App\Request\Http               $request,
        \Magento\Catalog\Model\ProductRepository          $productRepository,
        \Magento\Checkout\Model\Session                   $session,
        \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagement       
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->request = $request;
        $this->_productRepository = $productRepository;
        $this->_session = $session;
        $this->productLinkManagement = $productLinkManagement;
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
        if($this->request->getFullActionName() === 'catalog_product_view'){
            $productId = (int) $this->request->getParam('id');

            if($productId){
                $product = $this->_productRepository->getById($productId);
                
                $productdata = [];

                if($product->getTypeId() == 'simple'){
                    array_push($productdata, $product->getSku());
                }

                // configuration product logic
                if ($product->getTypeId() == "configurable") 
                {
                    $_children = $product->getTypeInstance()->getUsedProducts($product);
                    foreach ($_children as $child){
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

                $this->helper->inventorylog('PDP page API call: ' . json_encode($productdata), 'info');
                if(!empty($productdata)){
                    $this->erpInventory->inventoryApiCall($productdata);
                }
            }
        }

        if($this->request->getFullActionName() === 'checkout_cart_index'){
            $items = $this->_session->getQuote()->getAllItems();
            $productdata = [];
            foreach ($items as $key => $item) {
                    array_push($productdata, $item->getSku());
            }

            $this->helper->inventorylog('Cart page API call: ' . json_encode($productdata), 'info');
            if(!empty($productdata)){
                $this->erpInventory->inventoryApiCall($productdata);
            }
        }
    }
}
