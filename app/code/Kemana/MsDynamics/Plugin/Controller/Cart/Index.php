<?php

namespace Kemana\MsDynamics\Plugin\Controller\Cart;


class Index {

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Inventory
     */
    protected $erpInventory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Inventory $erpInventory
     * @param \Magento\Checkout\Model\Session $session 
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Inventory        $erpInventory,
        \Magento\Checkout\Model\Session                   $session 
    )
    {
        $this->helper = $helper;
        $this->erpInventory = $erpInventory;
        $this->_session = $session;
    }

    public function beforeExecute(
        \Magento\Checkout\Controller\Cart\Index $subject
    )
    {
        if (!$this->helper->isEnable()) {
            return;                                             
        }
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