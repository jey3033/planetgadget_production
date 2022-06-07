<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_GiftRegistry
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\GiftRegistry\Controller\Index;

/**
 * Class Index
 *
 * @package Magento\GiftRegistry\Controller\Index\Index
 */
class Index extends \Magento\GiftRegistry\Controller\Index\Index
{
    /**
     * View gift registry list in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('giftregistry_list');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Gift Registry List'));
        $this->_view->renderLayout();
    }
}
