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
 * Class CustomerAccountEdited
 */
class CustomerAccountEdited implements \Magento\Framework\Event\ObserverInterface
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
     * @return void
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

        $this->helper->log('CUSTOMER : Start Customer Account Edit Event.', 'info');

        $customer = $this->customerRepository->get($observer->getData('email'));

        if (!$customer->getCustomAttribute('ms_dynamic_customer_number')) {
            $this->helper->log('CUSTOMER : This customer has no ERP customer number. So unable to update in ERP.', 'info');
            $this->helper->log('CUSTOMER : End Customer Account Edit Event.', 'info');
            return;
        }

        if ($customer->getDefaultShipping()) {
            $this->helper->log('CUSTOMER : This customer has default shipping address. So he was updated while the Address Save After event. Skipped.', 'info');
            $this->helper->log('CUSTOMER : End Customer Account Edit Event.', 'info');
            return;
        }

        //TODO Remove this default DOB
        $dob = "1986-08-05";
        if ($customer->getDob()) {
            $dob = date("Y-m-d", strtotime($customer->getDob()));
        }

        $dataToCustomer = [
            "MagentoCustomerID" => $customer->getId(),
            "CustomerNo" => $customer->getCustomAttribute('phonenumber')->getValue(),
            "Name" => $customer->getFirstname(),
            "Name2" => $customer->getLastname(),
            "DoB" => $dob,
            "Email" => $customer->getEmail(),
        ];

        $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

        $updateCustomerInErp = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
            $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

        if (empty($updateCustomerInErp)) {
            $this->helper->log('CUSTOMER : ERP system might be off line', 'error');
            return;
        }

        if (isset($updateCustomerInErp['response']['CustomerNo'])) {
            //$this->updateCustomerMsDynamicNumber($customer->getId(), $updateCustomerInErp['response']['CustomerNo']);

            $this->helper->log('CUSTOMER : Customer ' . $customer->getId() . " updated successfully in ERP after Account Edit Success event", 'info');
        }
    }
}
