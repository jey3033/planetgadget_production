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
use Magento\Customer\Model\Context;

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
    protected $_context;

    /**
     * @type Session
     */
    protected $_customerSession;

    /**
     * @type CustomerFactory
     */
    protected $_customerFactory;

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
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_response            = $response;
        $this->_urlFactory          = $urlFactory;
        $this->_context             = $context;
        $this->_customerSession     = $customerSession;
        $this->_customerFactory     = $customerFactory;
    }
 
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $allowedRoutes = [
            'customer_account_edit',
            'customer_account_login',
            'customer_account_logoutsuccess',
            'customer_section_load'
        ];
        $customer = $this->_customerFactory->create();
        $request = $observer->getEvent()->getRequest();
        $actionFullName = strtolower($request->getFullActionName());
       
        if ($this->_customerSession->getId()) {
            $customer->load($this->_customerSession->getId());
            if ($customer->getPhonenumber() === '-'){
                if (!in_array($actionFullName, $allowedRoutes)){
                    $this->_response->setRedirect($this->_urlFactory->create()->getUrl('customer/account/edit'));
                }
            }
            
        }
    }
}