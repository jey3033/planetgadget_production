<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kredivo\Payment\Controller\Payment;

use \Magento\Sales\Model\Order;

class Response extends \Kredivo\Payment\Controller\Payment
{
    public function execute()
    {
    	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	        $this->_logger->debug('kredivo_debug:' . print_r($_GET, true));
	        if (isset($_GET['order_id']) && isset($_GET['tr_status'])) {
	            $trans_status = strtolower($_GET['tr_status']);
	            //if capture or pending or settlement, redirect to order received page
	            if (in_array($trans_status, ['settlement', 'pending'])) {
	                // $this->_checkoutSession->clearQuote();
	                $this->_redirect($this->getCheckoutSuccessUrl());
	            }
	            //if deny, redirect to order checkout page again
	            elseif ($trans_status == 'deny') {
	                $this->cancelAction();
	                $this->_redirect($this->getCheckoutFailureUrl());
	            }
	        }
	    }
    }

    private function cancelAction()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            $orderId = $this->_checkoutSession->getLastRealOrderId();
            $order   = $this->_orderFactory->loadByIncrementId($orderId);
            if ($order->getIncrementId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()
                    ->setState(Order::STATE_CANCELED)
                    ->setStatus('Kredivo has declined the payment.')
                    ->save();
            }
        }
    }
}
