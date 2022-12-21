<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Customer
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Customer\Observer;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Class RestrictSiteIfEmptyPhoneNumber
 *
 * @package Kemana\Customer\Observer
 */
class RestrictSiteIfEmptyPhoneNumber implements ObserverInterface
{
    
    /**
     * @type Http
     */
    protected $_response;

    /**
     * @type UrlFactory
     */
    protected $_urlFactory;

    /**
     * @type Context
     */
    protected $_httpContext;

    /**
     * @type Session
     */
    protected $_customerSession;

    /**
     * @type CustomerRepositoryInterface
     */
    protected $_customerRepository;

    /**
     * @param Http $response
     * @param UrlFactory $urlFactory
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Http\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->_response            = $response;
        $this->_urlFactory          = $urlFactory;
        $this->_httpContext         = $context;
        $this->_customerSession     = $customerSession;
        $this->_customerRepository  = $customerRepository;
    }
 
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        
        $this->setSessionHttpContext();

        $allowedRoutes = [
            'customer_account_edit',
            'customer_account_login',
            'customer_account_logoutsuccess',
            'customer_section_load'
        ];
        $request = $observer->getEvent()->getRequest();
        $actionFullName = strtolower($request->getFullActionName());
        $this->_httpContext->setValue(
        	'customer_id',
        	$this->_customerSession->getId(),
        	false
    	);
        $isCustomerLoggedIn = $this->_httpContext->getValue('customer_id');
        if ($isCustomerLoggedIn) {
            $customer = $this->_customerRepository->getById($this->_customerSession->getId());
            if ($customerPhoneNumber = $customer->getCustomAttribute('phonenumber')) { // check if phonenumber value is exists
                $customerPhoneNumberValue = $customerPhoneNumber->getValue();
            } else {
                $customerPhoneNumberValue = '-';
            }
            $this->_customerSession->setPhonenumber($customerPhoneNumberValue);
            if ($customerPhoneNumberValue === '-'){
                if (!in_array($actionFullName, $allowedRoutes)){
                    $this->_response->setRedirect($this->_urlFactory->create()->getUrl('customer/account/edit'));
                }
            }
        }
    }

    /**
     * Set session to Http Context after loggedin
     * @return void
     */
    public function setSessionHttpContext(){

        if ($this->_customerSession->isLoggedIn()) {
            if ($this->_httpContext->getValue('customer_id') != $this->_customerSession->getId()) {
                // set id current customer logged in
                $this->_httpContext->setValue(
                    'customer_id',
                    $this->_customerSession->getId(),
                    false
                );
            }
        }
    }
}
