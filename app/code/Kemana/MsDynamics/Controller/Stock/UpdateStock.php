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
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory   
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->request = $request;
        $this->_productRepository = $productRepository;
        $this->productLinkManagement = $productLinkManagement;
        $this->resultJsonFactory = $resultJsonFactory;

        return parent::__construct($context);
    }

    public function execute()
    {
        $productId = (int) $this->request->getParam('id');
        $resultJson = $this->resultJsonFactory->create();
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
                $response = $this->erpInventory->inventoryApiCall($productdata);
                return $resultJson->setData($response);
            }
        }
    }
}
