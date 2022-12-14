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
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Default TP number prefix
     */
    const DEFAULT_TP_PREFIX = '62000000000';

    /**
     * Cookie name for TP number
     */
    const COOKIE_NAME_IS_TP_NUMBER_VALID = 'COOKIE_IS_TP_NUMBER_VALID';

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface      $customerRepository,
        \Magento\Store\Model\StoreManagerInterface             $storeManager
    )
    {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
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

        if ($telephone && $telephone == self::DEFAULT_TP_PREFIX . $email) {
            if ($this->cookieManager->getCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID)) {
                $this->deleteCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID);
            }

            $this->setPublicCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID, 'FALSE');

        } else {
            if ($this->cookieManager->getCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID)) {
                $this->deleteCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID);
            }
            $this->setPublicCookie(self::COOKIE_NAME_IS_TP_NUMBER_VALID, 'TRUE');
        }
    }

    /**
     * @param $cookieName
     * @param $value
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setPublicCookie($cookieName, $value)
    {

        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(604800) // 7 Days
            //->setSecure(true)
            ->setPath('/')
            ->setHttpOnly(false); // Can be access via Javascript

        $this->cookieManager->setPublicCookie(
            $cookieName,
            $value,
            $metadata
        );

        return true;
    }

    /**
     * @param $cookieName
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function deleteCookie($cookieName)
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $metadata->setPath('/');

        $this->cookieManager->deleteCookie($cookieName, $metadata);

        return true;
    }
}
