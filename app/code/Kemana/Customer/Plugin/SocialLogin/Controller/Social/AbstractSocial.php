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

namespace Kemana\Customer\Plugin\SocialLogin\Controller\Social;

/**
 * Class AbstractSocial
 *
 * @package Kemana\SocialLogin\Controller
 */
class AbstractSocial
{

    /**
     * @param \Kemana\SocialLogin\Controller\Social\AbstractSocial $subject
     * @param $proceed
     * @param $userProfile
     * @param $websiteId
     * @param $type
     * @return bool|Customer|mixed
     */

    public function aroundCreateCustomerProcess(\Kemana\SocialLogin\Controller\Social\AbstractSocial $subject, callable $proceed, $userProfile, $websiteId = null, $type)
    {
        $name = explode(' ', $userProfile->displayName ?: __('New User'));

        $user = array_merge(
            [
                'email'      => $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com',
                'firstname'  => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
                'lastname'   => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
                'identifier' => $userProfile->identifier,
                'phonenumber'=> $userProfile->phone,
                'type'       => $type,
                'password'   => isset($userProfile->password) ? $userProfile->password : null
            ],
            $this->getUserData($userProfile)
        );

        if (!$user['phonenumber']) {
            $user['phonenumber'] = '0000000000'.$user['email'];
        }

        $user['dob'] = '01/10/1900';

        return $subject->createCustomer($user, $websiteId, $type);
    }

    /**
     * @param $profile
     *
     * @return array
     */
    public function getUserData($profile)
    {
        return [];
    }
}
