<?php

namespace Kemana\MsDynamics\Plugin\Controller\Product;


class View {

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
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Framework\App\Request\Http               $request,
        \Magento\Catalog\Model\ProductRepository          $productRepository,
        \Magento\Bundle\Api\ProductLinkManagementInterface $productLinkManagement       
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->request = $request;
        $this->_productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
    }

    public function beforeExecute(
        \Magento\Catalog\Controller\Product\View $subject
    )
    {
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
}