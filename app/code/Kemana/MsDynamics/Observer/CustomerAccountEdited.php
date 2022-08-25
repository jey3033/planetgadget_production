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
        $this->helper->log('Start Customer Account Edit Event.', 'info');

        $customer = $this->customerRepository->get($observer->getData('email'));

        if (!$customer->getCustomAttribute('ms_dynamic_customer_number')) {
            $this->helper->log('This customer has no ERP customer number. So unable to update in ERP.', 'info');
            $this->helper->log('End Customer Account Edit Event.', 'info');
            return;
        }

        if ($customer->getDefaultShipping()) {
            $this->helper->log('This customer has default shipping address. So he was updated while the Address Save After event. Skipped.', 'info');
            $this->helper->log('End Customer Account Edit Event.', 'info');
            return;
        }

        $dataToCustomer = [
            "magento_customer_id" => $customer->getId(),
            "customer_no" => $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue(),
            "phone_no" => $customer->getCustomAttribute('phonenumber')->getValue(),
            "name" => $customer->getFirstname(),
            "name_2" => $customer->getLastname(),
            "middle_name" => "",
            "dob" => "1986-08-05",
            "email" => $customer->getEmail(),
            "salutation" => "",
            "gender" => "",
            "created_date" => "",
            "address" => "",
            "address_2" => "",
            "city" => "",
            "postcode" => ""
        ];

        $updateCustomerInErp = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(), $dataToCustomer);

        if (isset($updateCustomerInErp[1]->customer_no)) {
            $this->helper->log('Customer ' . $customer->getId() . ' firstname and last name updated', 'info');
            $this->helper->log('End Customer Account Edit Event.', 'info');
        }
    }
}
