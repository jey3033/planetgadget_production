<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Reward
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Reward\Controller\Customer;

/**
 * Class Info
 *
 * @package Magento\Reward\Controller\Customer\Info
 */
class Info extends \Magento\Reward\Controller\Customer\Info
{
    
    /**
     * Info Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Points & Membership'));
        $this->_view->renderLayout();
    }
}
