<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Customer\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerLogin
 */
class CustomerLogin implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface      $customerRepository,
        \Magento\Store\Model\StoreManagerInterface             $storeManager,
        \Magento\Customer\Model\Session                        $customerSession
    )
    {
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $telephone = '';

        if ($observer->getEvent()->getName() == 'customer_account_edited') {
            $email = $observer->getEvent()->getEmail();
            $customer = $this->customerRepository->get($email, $this->storeManager->getStore()->getWebsiteId());

            if ($customer->getCustomAttribute('phonenumber')) {
                $telephone = $customer->getCustomAttribute('phonenumber')->getValue();
            }

        } else {
            $customer = $observer->getEvent()->getCustomer();
            $email = $customer->getEmail();
            $telephone = $customer->getPhonenumber();
        }

        $this->_customerSession->setPhonenumber($telephone);
    }
}
