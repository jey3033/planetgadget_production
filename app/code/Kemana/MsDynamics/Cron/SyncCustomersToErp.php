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

namespace Kemana\MsDynamics\Cron;

/**
 * Class SyncCustomersToErp
 */
class SyncCustomersToErp
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
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $customerGroupRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Kemana\MsDynamics\Model\Customer
     */
    protected $customer;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Kemana\MsDynamics\Model\Customer $customer
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer         $erpCustomer,
        \Magento\Customer\Api\GroupRepositoryInterface    $customerGroupRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface  $addressRepository,
        \Kemana\MsDynamics\Model\Customer                 $customer
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->customer = $customer;

    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function syncMissingCustomersFromRealTimeSync($cronObject, $customerId = null)
    {
        $singleCustomerFromGridResult = false;
        $erpCustomerNumber = 0;

        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('CUSTOMER : Start to process not synced customers to the ERP using the CRON JOB', 'info');

        $notSyncCustomers = $this->customer->getNoySyncCustomersInPg($customerId);

        if ($customerId) {
            $tmpNotSyncCustomers = $notSyncCustomers;
            $notSyncCustomers = null;
            $notSyncCustomers[] = $tmpNotSyncCustomers;
        }

        $this->helper->log('CUSTOMER : Retrieved ' . count($notSyncCustomers) . ' customers from database to process', 'info');

        foreach ($notSyncCustomers as $customer) {
            $this->helper->log('CUSTOMER : Started to process ' . $customer->getId() . " to send to the ERP by CRON JOB", 'info');

            // because phonenumber is required in ERP
            if (!$customer->getCustomAttribute('phonenumber')) {
                $this->helper->log('CUSTOMER : Skipped customer ' . $customer->getId() . ' due to no telephone number in Magento', 'info');
                continue;
            }

            if ($customer->getCustomAttribute('phonenumber') && $customer->getCustomAttribute('phonenumber')->getValue() == '62000000000'.$customer->getEmail()) {
                $this->helper->log('CUSTOMER : Skipped customer ' . $customer->getId() . ' due to telephone number equal to 62000000000'.$customer->getEmail().'. This customer can be came with social login.', 'info');
                continue;
            }

            $address = "";
            $address2 = "";
            $city = "";
            $postCode = "";

            if ($customer->getDefaultShipping()) {
                $defaultShippingAddress = $this->addressRepository->getById($customer->getDefaultShipping());

                $city = $defaultShippingAddress->getCity();
                $postCode = $defaultShippingAddress->getPostcode();

                if (isset($defaultShippingAddress->getStreet()[0])) {
                    $address = $defaultShippingAddress->getStreet()[0];
                }

                if (isset($defaultShippingAddress->getStreet()[1])) {
                    $address2 = $defaultShippingAddress->getStreet()[0];
                }
            }

            //TODO Remove this default DOB
            $dob = "1986-08-05";
            if ($customer->getDob()) {
                $dob = date("Y-m-d", strtotime($customer->getDob()));
            }
            // TODO END

            //$this->helper->log('CUSTOMER : Birthday set as  0000-00-00 in ERP for customer' . $customer->getId() . ' due to no birthday in Magento', 'info');

            $customerGroup = $this->customerGroupRepository->getById($customer->getGroupId());

            $dataToCustomer = [
                "MagentoCustomerID" => $customer->getId(),
                "CustomerNo" => $customer->getCustomAttribute('phonenumber')->getValue(),
                "Name" => $customer->getFirstname(),
                "Name2" => $customer->getLastname(),
                "DoB" => $dob,
                "Email" => $customer->getEmail(),
                "Address" => $address,
                "Address2" => $address2,
                "City" => $city,
                "Postcode" => $postCode
            ];

            $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

            $this->helper->log('CUSTOMER : Started to send customer ' . $customer->getId() . " to the ERP by CRON JOB", 'info');
            $createCustomerInErp = $this->erpCustomer->createCustomerInErp($this->helper->getFunctionCreateCustomer(),
                $this->helper->getSoapActionCreateCustomer(), $dataToCustomer);

            if (empty($createCustomerInErp)) {
                $this->helper->log('CUSTOMER : ERP system might be off line', 'error');
                continue;
            }

            if ($createCustomerInErp['curlStatus'] == 500 && $this->helper->checkAlreadyExistCustomerError($createCustomerInErp['response'])) {
                $this->helper->log('CUSTOMER : This customer already exist in ERP. So ERP customer number is updating in Magento', 'info');

                $updateCustomer = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
                    $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

                if (empty($updateCustomer)) {
                    $this->helper->log('CUSTOMER : ERP system might be off line', 'error');
                    continue;
                }

                if (isset($updateCustomer['response']['CustomerNo'])) {
                    $this->helper->updateCustomerMsDynamicNumber($customer->getId(), $updateCustomer['response']['CustomerNo']);

                    $this->helper->log('CUSTOMER : Customer ' . $customer->getId() . " updated successfully in ERP by Cron. Because this customer already exist in ERP", 'info');
                    $singleCustomerFromGridResult = true;
                    $erpCustomerNumber = $updateCustomer['response']['CustomerNo'];
                }
            }

            if (isset($createCustomerInErp['response']['CustomerNo'])) {

                if ($customerId) {
                    $this->helper->log('CUSTOMER : Customer successfully created from the SYNC button click from admin customer grid', 'info');
                }

                $this->helper->log('CUSTOMER : Customer successfully created by the CRON', 'info');

                $getCustomer = $this->customerRepository->getById($customer->getId());
                $getCustomer->setCustomAttribute('ms_dynamic_customer_number', $createCustomerInErp['response']['CustomerNo']);
                $this->customerRepository->save($getCustomer);
                $singleCustomerFromGridResult = true;

                $erpCustomerNumber = $createCustomerInErp['response']['CustomerNo'];

                if ($customerId) {
                    $this->helper->log('CUSTOMER : End SYNC button event from customer admin grid for ' . $customer->getId() . '. Sent to ERP', 'info');
                }

                $this->helper->log('CUSTOMER : End Cron job process for customer ' . $customer->getId() . '. sent to ERP by CRON', 'info');

            }
        }

        if ($customerId) {
            if ($singleCustomerFromGridResult) {
                return [
                    'result' => true,
                    'msDynamicCustomerNumber' => $erpCustomerNumber ?? '',
                ];
            }

            return [
                'result' => false
            ];
        }
    }
}
