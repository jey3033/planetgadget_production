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
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\Customer\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;
use \Magento\Framework\UrlFactory;

class PhoneNumberValidataion extends \Magento\Framework\View\Element\Template
{
    public $customerSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_urlFactory = $urlFactory;
    }

    public function getPhonenumber()
    {
        if($this->customerSession->getPhonenumber()){
            return $this->customerSession->getPhonenumber();
        }
        return null;
    }

    public function getRedirectionUrl(){
        return $this->_urlFactory->create()->getUrl('customer/account/edit');
    }
}   