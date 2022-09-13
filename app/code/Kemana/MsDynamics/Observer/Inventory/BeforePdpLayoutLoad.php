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
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     * @param \Kemana\MsDynamics\Helper\Data $helper
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Framework\App\Request\Http               $request,
        \Magento\Catalog\Model\ProductRepository          $productRepository
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->request = $request;
        $this->_productRepository = $productRepository;
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
                $productsku = $this->_productRepository->getById($productId)->getSku();
                $this->helper->inventorylog('PDP page API call: ' . $productsku, 'info');
                $productdata = [];
                array_push($productdata, $productsku);

                if(!empty($productdata)){
                    $this->erpInventory->inventoryApiCall($productdata);
                }
            }
        }
    }
}
