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
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer         $erpCustomer,
        \Magento\Customer\Api\GroupRepositoryInterface    $customerGroupRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder      $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder              $filterBuilder,
        \Magento\Customer\Api\AddressRepositoryInterface  $addressRepository
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->addressRepository = $addressRepository;

    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function syncMissingCustomersFromRealTimeSync()
    {
        $this->helper->log('Start to process not synced customers to the ERP using the CRON JOB', 'info');

        $filterErpCustomerNumber = $this->filterBuilder
            ->setField('ms_dynamic_customer_number')
            ->setConditionType('null')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filterErpCustomerNumber]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $notSyncCustomers = $this->customerRepository->getList($searchCriteria)->getItems();

        $this->helper->log('Retrieved ' . count($notSyncCustomers) . ' customers from database to process', 'info');

        foreach ($notSyncCustomers as $customer) {
            $this->helper->log('Started to process ' . $customer->getId() . " to send to the ERP by CRON JOB", 'info');

            // because phonenumber is required in ERP
            if (!$customer->getCustomAttribute('phonenumber')) {
                $this->helper->log('Skipped customer ' . $customer->getId() . ' due to no telephone number in Magento', 'info');
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
                $dob = $customer->getDob();
            }

            //$this->helper->log('Birthday set as  0000-00-00 in ERP for customer' . $customer->getId() . ' due to no birthday in Magento', 'info');

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

            $this->helper->log('Started to send customer ' . $customer->getId() . " to the ERP by CRON JOB", 'info');
            $createCustomerInErp = $this->erpCustomer->createCustomerInErp($this->helper->getFunctionCreateCustomer(),
                $this->helper->getSoapActionCreateCustomer(), $dataToCustomer);

            ///////////
            if ($createCustomerInErp['curlStatus'] == 500 && $this->helper->checkAlreadyExistCustomerError($createCustomerInErp['response'])) {
                $this->helper->log('This customer already exist in ERP. So ERP customer number is updating in Magento', 'info');

                $updateCustomer = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
                    $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

                if (isset($updateCustomer['response']['CustomerNo'])) {
                    $this->helper->updateCustomerMsDynamicNumber($customer->getId(), $updateCustomer['response']['CustomerNo']);

                    $this->helper->log('Customer ' . $customer->getId() . " updated successfully in ERP by Cron. Because this customer already exist in ERP", 'info');
                }
            }

            if (isset($createCustomerInErp['response']['CustomerNo'])) {

                $getCustomer = $this->customerRepository->getById($customer->getId());
                $getCustomer->setCustomAttribute('ms_dynamic_customer_number', $createCustomerInErp['response']['CustomerNo']);
                $this->customerRepository->save($getCustomer);

                $this->helper->log('Start Ack call for customer ' . $customer->getId() . ' by CRON', 'info');

                $ackCustomerData = [
                    "MagentoCustomerID" => $customer->getId(),
                    "CustomerNo" => $createCustomerInErp['response']['CustomerNo']
                ];

                $ackCustomerData = $this->helper->convertArrayToXml($ackCustomerData);

                $ackCustomer = $this->erpCustomer->ackCustomer($this->helper->getFunctionAckCustomer(),
                    $this->helper->getSoapActionAckCustomer(), $ackCustomerData);

                if ($ackCustomer['response']['CustomerNo']) {
                    $this->helper->log('Customer ' . $customer->getId() . " successfully sent to the ERP and AckCustomer call successfully done", 'info');
                }

                $this->helper->log('End Cron job process for customer ' . $customer->getId() . ' sent to ERP by CRON', 'info');

            }
            /// //////


        }
    }

}
