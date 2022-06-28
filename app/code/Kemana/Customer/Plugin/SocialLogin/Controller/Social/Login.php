<?php
/**
 * Copyright © 2020 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SocialLogin
 * @license  Proprietary
 *
 * @author   Aranga Wijesooriya <awijesooriya@kemana.com>
 */
namespace Kemana\Customer\Plugin\SocialLogin\Controller\Social;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

/**
 * Class Login
 *
 * @package Kemana\SocialLogin\Controller\Social
 */
class Login extends \Kemana\SocialLogin\Controller\Social\Login
{
    
    /**
     * @return ResponseInterface|Raw|ResultInterface|Login|void
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute()
    {
        
        if ($this->checkCustomerLogin() && $this->session->isLoggedIn()) {
            $this->_redirect('customer/account');

            return;
        }
        $type = $this->apiHelper->setType($this->getRequest()->getParam('type'));
        if (!$type) {
            $this->_forward('noroute');

            return;
        }

        try {
            $userProfile = $this->apiObject->getUserProfile($type);

            if (!$userProfile->identifier) {
                return $this->emailRedirect($type);
            }
        } catch (Exception $e) {
            $this->setBodyResponse($e->getMessage());

            return;
        }
        
        $websiteId = $this->getStore()->getWebsiteId();
        $customer = $this->apiObject->getCustomerBySocial($userProfile->identifier,$websiteId, $type);

        if (!$userProfile->email) {
            $message =  __('Email is Null, Please enter email in your %1 profile', $type);
            $this->messageManager->addErrorMessage($message);
            return $this->_appendJs();
        }

        $this->refresh($customer);

        if ($customer->getPhonenumber() === '-') {
            $message =  __('Invalid mobile phone number');
            $this->messageManager->addErrorMessage($message);
        }

        $customerData = $this->customerModel->load($customer->getId());
        if (!$customer->getId()) {
            $requiredMoreInfo = (int) $this->apiHelper->requiredMoreInfo();
            if ((!$userProfile->email && $requiredMoreInfo === 2) || $requiredMoreInfo === 1) {
                $this->session->setUserProfile($userProfile);

                return $this->_appendJs(
                    sprintf(
                        "<script>window.close();window.opener.fakeEmailCallback('%s','%s','%s');</script>",
                        $type,
                        $userProfile->firstName,
                        $userProfile->lastName
                    )
                );
            }
            $customer = $this->createCustomerProcess($userProfile, $websiteId, $type);
            
        }
        $this->refresh($customer);
        return $this->_appendJs();
    }
}
