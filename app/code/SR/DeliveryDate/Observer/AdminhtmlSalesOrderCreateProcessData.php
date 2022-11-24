<?php
namespace SR\DeliveryDate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AdminhtmlSalesOrderCreateProcessData implements ObserverInterface
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $requestData = $observer->getRequest();
        $deliveryDate = isset($requestData['delivery_date']) ? $requestData['delivery_date'] : null;
        $deliverytime = isset($requestData['delivery_time']) ? $requestData['delivery_time'] : null;

        /** @var \Magento\Sales\Model\AdminOrder\Create $orderCreateModel */
        $orderCreateModel = $observer->getOrderCreateModel();
        $quote = $orderCreateModel->getQuote();
        $quote->setDeliveryDate($deliveryDate);
        $quote->setDeliveryTime($deliverytime);

        return $this;
    }
}