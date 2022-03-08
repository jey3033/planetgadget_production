<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 
 namespace Kredivo\Payment\Controller\Payment;
 
 use \Kredivo\Payment\Library\Notification as Kredivo_Notification;
 use \Magento\Sales\Model\Order;
 
 class Status extends \Kredivo\Payment\Controller\Payment
 {
	public function execute()
    {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$notification = new Kredivo_Notification();
            $this->_logger->debug('kredivo_debug:' . $notification->get_json());
			
			$order = $this->_orderFactory->loadByIncrementId($notification->order_id);
			
			$response =  array (
			     'message' => 'Detail order status',
                 'order'   => array(
                    'status'    => strtoupper($order->getStatusLabel()),
                    'timestamp' => '',
                ),
			);			
			echo json_encode($response);
		}
		exit();
	}
 }