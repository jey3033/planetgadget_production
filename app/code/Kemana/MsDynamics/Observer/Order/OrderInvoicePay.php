<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Observer\Order;

/**
 * Class OrderInvoicePay
 */
class OrderInvoicePay implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Order
     */
    protected $erpOrder;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepositoryInterface;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResourceModel;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Order $erpOrder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data              $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Order      $erpOrder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Magento\Sales\Model\ResourceModel\Order    $orderResourceModel
    )
    {
        $this->helper = $helper;
        $this->erpOrder = $erpOrder;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderResourceModel = $orderResourceModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Order : Start Invoice success pay event to create the order in ERP', 'info');

        //$order = $this->orderRepositoryInterface->get($observer->getInvoice()->getOrder()->getIncrementId());
        $order = $observer->getInvoice()->getOrder();
        $orderItems = $order->getAllVisibleItems();

        if ($order->getIsSyncedToMsdynamicErp()) {
            $this->helper->log('Order : This order already synced. Magento order ID' . $order->getIncrementId(), 'info');
            return;
        }

        // Order will be sync when it is get paid total amount
        if ($observer->getInvoice()->getOrder()->getTotalDue()) {
            $this->helper->log('Order : This order still not fully paid. Magento order ID' . $order->getIncrementId(), 'info');
            return;
        }

        $this->helper->log('Order : Start to process the Magento order : ' . $order->getIncrementId(), 'info');

        $dataToOrder = [
            "OrderNo" => $order->getIncrementId(),
            "MagentoCustomerID" => $order->getCustomerId(),
            "PaymentMethod" => 'CASH',
            "TotalAmount" => floatval($order->getGrandTotal())
        ];

        if (floatval($order->getDiscountAmount())) {
            $dataToOrder['DiscountAmount'] = abs(floatval($order->getDiscountAmount()));
        }

        $getSourceLocationName = false;
        if ($order->getShippingMethod() == 'instore_pickup') {
            $getSourceLocationName = $this->helper->getSourceLocationCodeByName($order->getShippingAddress()->getFirstname());
        }

        $dataToOrder = $this->helper->convertArrayToXml($dataToOrder);

        if (!floatval($order->getDiscountAmount())) {
            $dataToOrder .= "<DiscountAmount>0</DiscountAmount>";
        }

        $dataToOrderLineItems = "<SalesOrderLine>";

        $lineNo = 1;
        $orderItemTotal = 0;
        foreach ($orderItems as $lineItem) {

            $dataToOrderLineItems .= "<Order_Line>";

            $dataToItem = [
                "OrderNo" => $order->getIncrementId(),
                "LineNo" => $lineNo,
                "ItemNo" => $lineItem->getSku(),
                "Quantity" => $lineItem->getQtyOrdered(),
                "Price" => floatval($lineItem->getPrice())
            ];

            if ($lineItem->getName()) {
                $dataToItem['Description'] = $lineItem->getName();
            }

            if ($getSourceLocationName) {
                $dataToItem['LocationCode'] = $getSourceLocationName;
            }

            $dataToItem = $this->helper->convertArrayToXml($dataToItem);

            $dataToOrderLineItems .= $dataToItem;
            $dataToOrderLineItems .= "</Order_Line>";

            $lineNo++;
            $orderItemTotal = $orderItemTotal + floatval($lineItem->getRowTotalInclTax());
        }

        $dataToOrderLineItems .= "</SalesOrderLine>";

        $dataToOrder .= $dataToOrderLineItems;

        $this->helper->log('Order : Order Total : ' . floatval($order->getGrandTotal()) . ' and Order Line Items Total :' . $orderItemTotal, 'error');

        $createOrderInErp = $this->erpOrder->createOrderInErp($this->helper->getFunctionCreateOrder(),
            $this->helper->getSoapActionCreateOrder(), $dataToOrder);

        if (empty($createOrderInErp)) {
            $this->helper->log('Order : ERP system might be off line', 'error');
            return;
        }

        if ($createOrderInErp['curlStatus'] == 200 && isset($createOrderInErp['response']['OrderNo'])) {
            $this->helper->log('Order : Magento Order ' . $createOrderInErp['response']['OrderNo'] . ' successfully sent to ', 'info');

            $order->setIsSyncedToMsdynamicErp(1);
            $this->orderResourceModel->save($order);

            $this->helper->log('Order : Successfully updated the is_synced_to_msdynamic_erp attribute for Magento Order ' . $createOrderInErp['response']['OrderNo'], 'info');

            $this->helper->log('Order : End process the Magento order : ' . $order->getIncrementId() . '. Successfully sent to ERP', 'info');

        }
    }

}

