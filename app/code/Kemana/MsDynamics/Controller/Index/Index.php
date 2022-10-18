<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_StockAvai
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Controller\Index;

use Magento\Framework\Controller\Result\JsonFactory;
use Kemana\StockAvailabilityPopup\Model\Stock\SourceDataForSku;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Detail
 */
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $customer;
    protected $helper;
    protected $syncCustomersToErp;
    protected $syncCustomersFromErp;
    protected $syncOrdersToErp;

    public function __construct(
        \Magento\Framework\App\Action\Context        $context,
        \Magento\Framework\View\Result\PageFactory   $pageFactory,
        \Kemana\MsDynamics\Model\Api\Erp\Customer    $customer,
        \Kemana\MsDynamics\Helper\Data               $helper,
        \Kemana\MsDynamics\Cron\SyncCustomersToErp   $syncCustomersToErp,
        \Kemana\MsDynamics\Cron\SyncCustomersFromErp $syncCustomersFromErp,
        \Kemana\MsDynamics\Cron\SyncOrdersToErp $syncOrdersToErp
    )
    {
        $this->customer = $customer;
        $this->_pageFactory = $pageFactory;
        $this->helper = $helper;
        $this->syncCustomersToErp = $syncCustomersToErp;
        $this->syncCustomersFromErp = $syncCustomersFromErp;
        $this->syncOrdersToErp = $syncOrdersToErp;

        return parent::__construct($context);
    }

    public function execute()
    {

        /*$syncOrdersToErp = $this->syncOrdersToErp->syncOrdersFromMagentoToErp();

        exit;

        $d = 0;
        $this->syncCustomersFromErp->syncCustomersFromErpToMagento();
        echo "Donee";
        exit;

        $this->syncCustomersToErp->syncMissingCustomersFromRealTimeSync();
        echo "Done";
        exit;*/
        // Get Customer By ID
        /* $customerInErp = $this->customer->getCustomerInErp($this->helper->getFunctionGetCustomer(), '60215644000');
         echo "done";
         exit;*/
        //json_decode($customerInErp['return_value'])[1][0]

        //Create a customer
        /* $customerData = [
             "magento_customer_id" => "11111003",
             "phone_no" => "6281112341003",
             "name" => "Achintha",
             "name_2" => "Madushan",
             "middle_name" => "Paliwaththa",
             "dob" => "1986-08-05",
             "email" => "amadushan3@kemana.com",
             "salutation" => "",
             "gender" => "0",
             "created_date" => "2022-08-22",
             "club_code" => "GOLD",
             "address" => "",
             "address_2" => "",
             "city" => "DENPASRA",
             "postcode" => ""
         ];
         $newCustomer = $this->customer->createCustomerInErp($this->helper->getFunctionCreateCustomer(), $customerData);//081236009294

         return $newCustomer;*/

        //Update Customer
        /*$customerData = [
            "magento_customer_id" => "11111002",
            "customer_no" => "6281112341002"
        ];

        $newCustomer = $this->customer->ackCustomer($this->helper->getFunctionAckCustomer(), $customerData);//081236009294*/


        /*$customerData = [
            "magento_customer_id" => "11111002",
            "customer_no" => "6281112341003",
            "phone_no" => "6281112341002",
            "name" => "Achintha",
            "name_2" => "Madushan",
            "middle_name" => "Kankanamge",
            "dob" => "1986-08-05",
            "email" => "amadushan2@kemana.com",
            "salutation" => "",
            "gender" => "0",
            "created_date" => "2022-08-22",
            "club_code" => "GOLD",
            "address" => "",
            "address_2" => "",
            "city" => "DENPASRA",
            "postcode" => ""
        ];*/
        /*  $dataToCustomer = [
              "magento_customer_id" => "11111002",
              "customer_no" => "6281112341003",
              "name" => 'DDDDDDDDD',
              "name_2" => 'FFFFFFFFFFFFFFf',
              "middle_name" => "",
              "dob" => "1986-08-05",
              "email" => "amadushan2@kemana.com",
              "address" => 'FFGFF ss fs fd sdfsdf',
              "address_2" => 'rfswf esfw fwefwef',
              "city" => 'rrfef',
              "postcode" => '4545435'
          ];*/
        // $newCustomer = $this->customer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(), $dataToCustomer);//081236009294


    }
}
