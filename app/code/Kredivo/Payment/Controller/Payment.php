<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kredivo\Payment\Controller;

/**
 * Responsible for loading page content.
 *
 * This is a basic controller that only loads the corresponding layout file. It may duplicate other such
 * controllers, and thus it is considered tech debt. This code duplication will be resolved in future releases.
 */

abstract class Payment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->init();
    }

    private function init()
    {
        $this->_checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $this->_storeManager    = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_orderFactory    = $this->_objectManager->create('Magento\Sales\Model\Order');
        $this->_logger          = $this->_objectManager->get('Psr\Log\LoggerInterface');
        $this->_scopeConfig     = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');

    }

    protected function getConfig($field)
    {
        $path = 'payment/' . $this->getCode() . '/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function getCOde()
    {
        return \Kredivo\Payment\Model\KredivoPayment::CODE;
    }

    /**
     * Helper Function
     */

    protected function getServerKey()
    {
        return $this->getConfig('server_key');
    }

    protected function getEnvironment()
    {
        return $this->getConfig('environment') == 'production' ? true : false;
    }

    protected function getConversionRate()
    {
        return $this->getConfig('conversion_rate');
    }

    protected function getOrderStatus()
    {
        return $this->getConfig('order_status');
    }

    protected function getResponseUrl()
    {
        return $this->_scopeConfig->getValue('web/secure/base_url').'kredivo/payment/response';
    }

    protected function getNotificationUrl()
    {
        return $this->_scopeConfig->getValue('web/secure/base_url').'kredivo/payment/notification';
    }

	protected function getStatusUrl()
    {
        return $this->_scopeConfig->getValue('web/secure/base_url').'kredivo/payment/status';
    }

    protected function getCheckoutSuccessUrl()
    {
        return $this->_url->getUrl('checkout/onepage/success', ['_secure' => false]);
    }

    protected function getCheckoutFailureUrl()
    {
        return $this->_url->getUrl('checkout/onepage/failure', ['_secure' => false]);
    }
}
