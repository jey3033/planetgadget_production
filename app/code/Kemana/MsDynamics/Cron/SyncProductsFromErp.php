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

namespace Kemana\MsDynamics\Cron;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Class SyncProductsFromErp
 */
class SyncProductsFromErp
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                      $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer           $erpCustomer
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
    }

    /**
     * @throws InputMismatchException
     * @throws InputException
     * @throws LocalizedException
     */
    public function syncProductsFromErpToMagento()
    {   
        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Started to get the not synced product from ERP and then create product in Magento using Cron Job', 'info');

        $dataToGetProducts = [
            "Field" => "Synced",
            "Criteria" => "false"
        ];

        $dataToGetProducts = $this->helper->convertArrayToXml($dataToGetProducts);


        $getProductsFromErp = $this->erpCustomer->getUnSyncCustomersFromErp($this->helper->getFunctionProductList(),
            $this->helper->getSoapActionGetProductList(), $dataToGetProducts);
        print_r($getProductsFromErp);die;

        if (!is_array($getProductsFromErp) || !count($getProductsFromErp)) {
            $this->helper->log('No product received from ERP to create product in Magento', 'error');
            return;
        }
    }

}
