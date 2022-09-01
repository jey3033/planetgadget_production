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

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Class SyncCustomersFromErp
 */
class SyncCustomersFromErp
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
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $magentoCustomer;

    /**
     * @var \Kemana\MsDynamics\Model\Customer
     */
    protected $customer;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Model\Customer $magentoCustomer
     * @param \Kemana\MsDynamics\Model\Customer $customer
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                      $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer           $erpCustomer,
        \Magento\Customer\Api\CustomerRepositoryInterface   $customerRepository,
        \Magento\Customer\Api\AddressRepositoryInterface    $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory  $addressFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Model\Customer                    $magentoCustomer,
        \Kemana\MsDynamics\Model\Customer                   $customer
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->magentoCustomer = $magentoCustomer;
        $this->customer = $customer;
    }

    /**
     * @throws InputMismatchException
     * @throws InputException
     * @throws LocalizedException
     */
    public function syncCustomersFromErpToMagento()
    {
        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Started to get the not synced customers from ERP and then create in Magento using Cron Job', 'info');

        $dataToGetCustomers = [
            "Field" => "Synced",
            "Criteria" => "false"
        ];

        $dataToGetCustomers = $this->helper->convertArrayToXml($dataToGetCustomers);

        $getCustomersFromErp = $this->erpCustomer->getUnSyncCustomersFromErp($this->helper->getFunctionCustomerList(),
            $this->helper->getSoapActionGetCustomerList(), $dataToGetCustomers);

        if (!is_array($getCustomersFromErp) || !count($getCustomersFromErp)) {
            $this->helper->log('No customers received from ERP to register in Magento', 'error');
            return;
        }

        $this->helper->log('Received ' . count($getCustomersFromErp['response']) . ' to register in Magento from ERP', 'info');

        $ackCustomerData = [];
        $newCustomerId = 0;

        $i = 0;
        foreach ($getCustomersFromErp['response'] as $erpCustomer) {
            // TODO Remove this
            if ($i > 35) {
                break;
            }
            // TODO END

            if ((!isset($erpCustomer['CustomerNo']) || !$erpCustomer['CustomerNo'])
                || (!isset($erpCustomer['Email']) || !$erpCustomer['Email'])
                || (!isset($erpCustomer['Name']) || !$erpCustomer['Name'])
                || (!isset($erpCustomer['PhoneNo']) || !$erpCustomer['PhoneNo'])) {

                $this->helper->log('CustomerNo, Email, Name or PhoneNo  missing for this customer in ERP API. Cannot create the customer in Magento. ' . json_encode($erpCustomer), 'error');
                continue;
            }

            try {

                if ($this->customer->checkExistEmailOrPhoneNumber($erpCustomer['Email'], $erpCustomer['PhoneNo'])) {
                    $this->helper->log('Email or PhoneNumber already exist in Magento for this customer. Aborting. Email : ' .
                        $erpCustomer['Email'] . ' PhoneNumber : ' . $erpCustomer['PhoneNo'], 'error');

                    continue;
                }

                $this->helper->log('Started to create the customer account in Magento for ERP customer : ' . $erpCustomer['CustomerNo'], 'info');
                $customer = $this->customerFactory->create();

                // Get Website ID
                //$websiteId = $this->storeManager->getWebsite()->getWebsiteId();

                $websiteId = 1;

                $nameArray = explode(" ", $erpCustomer['Name'], 2);

                // Preparing data for new customer
                $customer->setWebsiteId($websiteId);
                $customer->setEmail('567' . $erpCustomer['Email'] . '123');
                $customer->setFirstname($nameArray[0] ?? "");
                $customer->setLastname($nameArray[1] ?? ".");
                $customer->setCustomAttribute('phonenumber', $erpCustomer['PhoneNo']);
                $customer->setCustomAttribute('ms_dynamic_customer_number', $erpCustomer['CustomerNo']);

                // create the new customer and send the email
                $newCustomer = $this->customerRepository->save($customer);
                $newCustomerId = $newCustomer->getId();

                // TODO un comment this when production
                //$getNewCustomer = $this->magentoCustomer->load($newCustomerId);
                //$getNewCustomer->sendNewAccountEmail();
                // TODO END

                $this->helper->log('Successfully created the customer account in Magento for ERP customer : ' . $erpCustomer['CustomerNo'], 'info');

                try {

                    // TODO In ERP side post code is not available. But in Magento post code is required for address
                    if (!isset($erpCustomer['Postcode']) || !$erpCustomer['Postcode']) {
                        $erpCustomer['Postcode'] = 10110;
                    }
                    // TODO END

                    if (!isset($nameArray[1]) || !isset($erpCustomer['Postcode']) || !$erpCustomer['Postcode']) {
                        $this->helper->log('Last name or Postcode missing for this customer. Aborting. Email : ' . $erpCustomer['Email']);
                        continue;
                    }

                    $this->helper->log('Started to create the address in Magento for ERP customer : ' . $erpCustomer['CustomerNo'], 'info');

                    $address = $this->addressFactory->create();

                    $address->setFirstname($nameArray[0] ?? "")
                        ->setLastname($nameArray[1] ?? ".")
                        ->setCountryId('ID')
                        ->setCity($erpCustomer['City'] ?? "")
                        ->setPostcode($erpCustomer['Postcode'])
                        ->setCustomerId($newCustomerId)
                        ->setStreet([$erpCustomer['Address']])
                        ->setTelephone($erpCustomer['PhoneNo'])
                        ->setIsDefaultBilling(true)
                        ->setIsDefaultShipping(true);

                    $this->addressRepository->save($address);

                    $this->helper->log('Successfully created the address account in Magento for ERP customer : ' . $erpCustomer['CustomerNo'], 'info');

                    $this->helper->log('Started to update the Magento Customer ID in ERP for ERP customer : ' . $erpCustomer['CustomerNo'], 'info');

                    $dataToCustomer = [
                        "MagentoCustomerID" => $newCustomerId,
                        "CustomerNo" => $erpCustomer['CustomerNo'],
                    ];

                    $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

                    $updateCustomerInErp = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
                        $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

                    if (isset($updateCustomerInErp['response']['CustomerNo'])) {

                        $this->helper->log('Magento customer ID successfully updated for ERP customer in ERP and data collected for Ack call ' . $erpCustomer['CustomerNo'], 'error');

                        //collect data for Ack call
                        $ackCustomerData[] = [
                            "MagentoCustomerID" => $newCustomerId,
                            "CustomerNo" => $erpCustomer['CustomerNo']
                        ];
                    } else {
                        $this->helper->log('ERP Customer ' . $erpCustomer['CustomerNo'] . ' created in Magento. But when updating the
                        Magento customer number in ERP it failed. See last log message to check the issue.', 'info');
                    }

                } catch (\Exception $e) {
                    $this->helper->log('Unable to create the address for EPR customer number ' . $erpCustomer['CustomerNo'] . ' in Magento. Error : ' . $e->getMessage(), 'error');
                }

            } catch (\Exception $e) {
                $this->helper->log('Customer number ' . $erpCustomer['CustomerNo'] . ' failed to register in Magento. Error : ' . $e->getMessage(), 'error');
            }
            // TODO Remove this
            $i++;
            // TODO END
        }

        // Ack call

        if (empty($ackCustomerData)) {
            return;
        }

        $this->helper->log('Start Ack call for customers by CRON', 'info');

        $ackCustomerData = $this->helper->convertAckCustomerListToXml($ackCustomerData);

        $ackCustomer = $this->erpCustomer->ackCustomer($this->helper->getFunctionAckCustomer(),
            $this->helper->getSoapActionAckCustomer(), $ackCustomerData);

        if ($ackCustomer['responseStatus'] == '100') {
            $this->helper->log('Ack call successfully done for below customers' . $ackCustomerData, 'info');
            $this->helper->log('End to get the not synced customers from ERP and then create in Magento using Cron Job', 'info');
            return $ackCustomerData;
        }

    }

}
