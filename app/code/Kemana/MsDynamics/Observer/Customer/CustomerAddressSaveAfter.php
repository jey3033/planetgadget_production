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
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Observer\Customer;

/**
 * Class CustomerRegisterSuccess
 */
class CustomerAddressSaveAfter implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer         $erpCustomer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ((isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME'] == 'bin/magento')
            || (isset($_SERVER['SHELL']) && $_SERVER['SCRIPT_NAME'] == '/bin/bash')) {
            return;
        }

        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('CUSTOMER : Start Customer Address Save After Event.', 'info');

        $customerAddress = $observer->getEvent()->getCustomerAddress();

        if (!$customerAddress->getDefaultShipping()) {
            $this->helper->log('CUSTOMER : This is not a default shipping address. So Aborting.', 'info');
            $this->helper->log('CUSTOMER : End Customer Address Success.', 'info');
            return;
        }

        $address1 = "";
        $address2 = "";

        if (isset($customerAddress->getStreet()[0])) {
            $address1 = $customerAddress->getStreet()[0];
        }

        if (isset($customerAddress->getStreet()[1])) {
            $address2 = $customerAddress->getStreet()[1];
        }


        $getCustomer = $this->customerRepository->getById($customerAddress->getCustomerId());
        $erpCustomerNumber = $getCustomer->getCustomAttribute('ms_dynamic_customer_number');

        if (!$erpCustomerNumber) {
            $this->helper->log('CUSTOMER : Customer ID ' . $customerAddress->getCustomerId() . ' not have ERP customer number', 'info');
            $this->helper->log('CUSTOMER : Start Customer Address Save After Event.', 'info');
            return;
        }

        //TODO Remove this default DOB
        $dob = "1986-08-05";
        if ($getCustomer->getDob()) {
            $dob = date("Y-m-d", strtotime($getCustomer->getDob()));
        }

        $dataToCustomer = [
            "MagentoCustomerID" => $getCustomer->getId(),
            "CustomerNo" => $getCustomer->getCustomAttribute('phonenumber')->getValue(),
            "Name" => $getCustomer->getFirstname(),
            "Name2" => $getCustomer->getLastname(),
            "DoB" => $dob,
            "Address" => $address1,
            "Address2" => $address2,
            "City" => $customerAddress->getCity(),
            "Postcode" => $customerAddress->getPostcode()
        ];

        $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

        $updateCustomerInErp = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
            $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

        if (empty($updateCustomerInErp)) {
            $this->helper->log('CUSTOMER : ERP system might be off line', 'error');
            return;
        }

        if (isset($updateCustomerInErp['response']['CustomerNo'])) {
            $this->helper->log('CUSTOMER : Customer ID ' . $customerAddress->getCustomerId() . ' updated with address', 'info');
            $this->helper->log('CUSTOMER : End Customer Address Save After Event.', 'info');
        }

        return true;

    }

}
