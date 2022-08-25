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

        $customerGroup = $this->customerGroupRepository->getById($customer->getGroupId());

        $dataToCustomer = [
            "magento_customer_id" => $customer->getId(),
            "phone_no" => $customer->getCustomAttribute('phonenumber')->getValue(),
            "name" => $customer->getFirstname(),
            "name_2" => $customer->getLastname(),
            "middle_name" => "",
            "dob" => "1986-08-05",
            "email" => $customer->getEmail(),
            "salutation" => "",
            "gender" => "",
            "created_date" => "",
            "club_code" => strtoupper($customerGroup->getCode()),
            "address" => "",
            "address_2" => "",
            "city" => "",
            "postcode" => ""
        ];

        $createCustomerInErp = $this->erpCustomer->createCustomerInErp($this->helper->getFunctionCreateCustomer(), $dataToCustomer);

        if (isset($createCustomerInErp[0]->status_code) && $createCustomerInErp[0]->status_code == 901) {
            $this->helper->log('This customer already exist in ERP. So ERP customer number is updating in Magento', 'info');

            $dataToCustomer['customer_no'] = $createCustomerInErp[1]->customer_no;
            $updateCustomer = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(), $dataToCustomer);

            if ($updateCustomer[1]->customer_no) {
                $this->helper->log('Customer ' . $customer->getId() . " updated successfully in ERP after Successfully Register event. Because this customer already exist in the ERP", 'info');
            }
        }

        if (isset($createCustomerInErp[1]->customer_no)) {

            $getCustomer = $this->customerRepository->getById($customer->getId());
            $getCustomer->setCustomAttribute('ms_dynamic_customer_number', $createCustomerInErp[1]->customer_no);
            $this->customerRepository->save($getCustomer);

            $ackCustomerData = [
                "magento_customer_id" => $customer->getId(),
                "customer_no" => $createCustomerInErp[1]->customer_no
            ];

            $ackCustomer = $this->erpCustomer->ackCustomer($this->helper->getFunctionAckCustomer(), $ackCustomerData);

            if ($ackCustomer[1]->customer_no) {
                $this->helper->log('End Customer Register Success Event successfully and customer ' . $customer->getId() . ' sent to ERP', 'info');
            }
        }

    }

}
