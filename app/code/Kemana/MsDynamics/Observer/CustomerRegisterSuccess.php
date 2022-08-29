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

namespace Kemana\MsDynamics\Observer;

/**
 * Class CustomerRegisterSuccess
 */
class CustomerRegisterSuccess implements \Magento\Framework\Event\ObserverInterface
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
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer         $erpCustomer,
        \Magento\Customer\Api\GroupRepositoryInterface    $customerGroupRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerRepository = $customerRepository;
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
        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Start Customer Register Success Event', 'info');

        $customer = $observer->getEvent()->getCustomer();

        $dataToCustomer = [
            "MagentoCustomerID" => $customer->getId(),
            "CustomerNo" => $customer->getCustomAttribute('phonenumber')->getValue(),
            "Name" => $customer->getFirstname(),
            "Name2" => $customer->getLastname(),
            "MiddleName" => "",
            "DoB" => "1986-08-05",
            "Email" => $customer->getEmail(),
            "Salutation" => "",
            "Gender" => "",
            "Address" => "",
            "Address2" => "",
            "City" => "",
            "Postcode" => ""
        ];

        $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

        $createCustomerInErp = $this->erpCustomer->createCustomerInErp($this->helper->getFunctionCreateCustomer(),
            $this->helper->getSoapActionCreateCustomer(), $dataToCustomer);

        if ($createCustomerInErp['curlStatus'] == 500 && $this->helper->checkAlreadyExistCustomerError($createCustomerInErp['response'])) {
            $this->helper->log('This customer already exist in ERP. So ERP customer number is updating in Magento', 'info');

            $updateCustomer = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
                $this->helper->getSoapActionUpdateCustomer(),$dataToCustomer);

            if (isset($updateCustomer['response']['CustomerNo'])) {
                $this->updateCustomerMsDynamicNumber($customer->getId(), $updateCustomer['response']['CustomerNo']);

                $this->helper->log('Customer ' . $customer->getId() . " updated successfully in ERP after Successfully Register event. Because this customer already exist in the ERP", 'info');
            }
        }

        if (isset($createCustomerInErp['response']['CustomerNo'])) {

            $this->updateCustomerMsDynamicNumber($customer->getId(), $createCustomerInErp['response']['CustomerNo']);

            $this->helper->log('End Customer Register Success Event successfully and customer ' . $customer->getId() . ' sent to ERP', 'info');

        }

    }

    /**
     * @param $customerId
     * @param $customerNumber
     * @return bool
     */
    private function updateCustomerMsDynamicNumber($customerId, $customerNumber): bool
    {
        try {
            $getCustomer = $this->customerRepository->getById($customerId);
            $getCustomer->setCustomAttribute('ms_dynamic_customer_number', $customerNumber);
            $this->customerRepository->save($getCustomer);

            return true;
        } catch (\Exception $e) {
            $this->helper->log('End Customer Register Success Event - Failed to update Customer Number in Magento for Customer ' . $customerId . ' sent/update to ERP. Error :'.$e->getMessage(), 'info');
        }

        return false;
    }

}
