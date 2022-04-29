<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kredivo\Payment\Controller\Payment;

use \Kredivo\Payment\Library\Api as Kredivo_Api;
use \Kredivo\Payment\Library\Config as Kredivo_Config;
use \Kredivo\Payment\Library\Notification as Kredivo_Notification;
use \Magento\Sales\Model\Order;

class Notification extends \Kredivo\Payment\Controller\Payment
{
    public function execute()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            Kredivo_Config::$is_production = $this->getEnvironment();
            Kredivo_Config::$server_key    = $this->getServerKey();

            $notification = new Kredivo_Notification();
            $this->_logger->debug('kredivo_debug:' . $notification->get_json());

            $confirmation = Kredivo_Api::confirm_order_status(array(
                'transaction_id'=> $notification->transaction_id,
                'signature_key' => $notification->signature_key,
            ));
            $this->_logger->debug('kredivo_debug:' . print_r($confirmation, true));

            if (strtolower($confirmation->status) == 'ok') {

                $order = $this->_orderFactory->loadByIncrementId($confirmation->order_id);

                $fraud_status = strtolower($confirmation->fraud_status);
                if ($fraud_status == 'accept') {

                    $transaction_status = strtolower($confirmation->transaction_status);
                    switch ($transaction_status) {
                        case 'settlement':
	                        $invoice = $order->prepareInvoice();
	                        $invoice->register();
	                        $order->addRelatedObject($invoice);
                           
                            $order->setStatus(Order::STATE_PROCESSING);
                            // $order->sendOrderUpdateEmail(true, 'Thank you, your payment is successfully processed.');
                            break;
                        case 'pending':
                            $order->setStatus(Order::STATE_PENDING_PAYMENT);
                            // $order->sendOrderUpdateEmail(true, 'Thank you, your payment is successfully processed.');
                            break;
                        case 'deny':
                            $order->setStatus(Order::STATE_CANCELED);
                            break;
                        case 'cancel':
                            $order->setStatus(Order::STATE_CANCELED);
                            break;
                    }

                } else {
                    $order->setStatus(Order::STATUS_FRAUD);
                }

                $order->save();
            }

            echo Kredivo_Api::response_notification();
        }
        exit();
    }
}
