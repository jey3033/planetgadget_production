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
     * @var \Kemana\MsDynamics\Model\Api\Erp\Reward
     */
    protected $erpReward;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer         $erpCustomer,
        \Magento\Customer\Api\GroupRepositoryInterface    $customerGroupRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerRepository = $customerRepository;
        $this->erpReward = $erpReward;
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

        $this->helper->log('CUSTOMER : Start Customer Register Success Event', 'info');

        $customer = $observer->getEvent()->getCustomer();

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
            "MiddleName" => "",
            "DoB" => $dob,
            "Email" => $customer->getEmail()
        ];

        $dataToCustomer = $this->helper->convertArrayToXml($dataToCustomer);

        $createCustomerInErp = $this->erpCustomer->createCustomerInErp($this->helper->getFunctionCreateCustomer(),
            $this->helper->getSoapActionCreateCustomer(), $dataToCustomer);

        if (empty($createCustomerInErp)) {
            $this->helper->log('CUSTOMER : ERP system might be off line', 'error');
            return;
        }

        if ($createCustomerInErp['curlStatus'] == 500 && $this->helper->checkAlreadyExistCustomerError($createCustomerInErp['response'])) {
            $this->helper->log('CUSTOMER : This customer already exist in ERP. So ERP customer number is updating in Magento', 'info');

            $updateCustomer = $this->erpCustomer->updateCustomerInErp($this->helper->getFunctionUpdateCustomer(),
                $this->helper->getSoapActionUpdateCustomer(), $dataToCustomer);

            if (isset($updateCustomer['response']['CustomerNo'])) {
                $this->helper->updateCustomerMsDynamicNumber($customer->getId(), $updateCustomer['response']['CustomerNo']);
                $this->erpReward->addCustomerEarnPointToErp($customer->getId(), $createCustomerInErp['response']['CustomerNo']);

                $this->helper->log('CUSTOMER : Customer ' . $customer->getId() . " updated successfully in ERP after Successfully Register event. Because this customer already exist in the ERP", 'info');
            }
        }

        if (isset($createCustomerInErp['response']['CustomerNo'])) {

            $this->helper->updateCustomerMsDynamicNumber($customer->getId(), $createCustomerInErp['response']['CustomerNo']);
            $this->erpReward->addCustomerEarnPointToErp($customer->getId(), $createCustomerInErp['response']['CustomerNo']);

            $this->helper->log('CUSTOMER : End Customer Register Success Event successfully and customer ' . $customer->getId() . ' sent to ERP', 'info');

        }
    }
}
