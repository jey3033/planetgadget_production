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

namespace Kemana\Customer\Plugin\Customer\Controller\Account;

/**
 * Class EditPost
 */
class EditPost
{
    
    /**
     * * before save postrequest value convert first phonenumber value
     * @param \Magento\Customer\Controller\Account\EditPost $subjects
     */
    public function beforeExecute(\Magento\Customer\Controller\Account\EditPost $subjects)
    {
        if($subjects->getRequest()->getPost('phonenumber')){
            $phonenumber = $subjects->getRequest()->getPost('phonenumber');
            $pnFirstDigit = substr($phonenumber, 0, 1); // get first digit in phonenumber
            
            if($pnFirstDigit == 0){
                $ptn = "/^0/";  // Regex first digit if 0
                $rpltxt = "62";  // Replacement string
                $newphonenumber =  preg_replace($ptn, $rpltxt, $phonenumber); // replace first digit if 0
                $subjects->getRequest()->setPostValue('phonenumber', $newphonenumber);
            }
        }
    }
}
