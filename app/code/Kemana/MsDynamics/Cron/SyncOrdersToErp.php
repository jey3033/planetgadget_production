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

namespace Kemana\MsDynamics\Cron;

/**
 * Class SyncOrdersToErp
 */
class SyncOrdersToErp
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
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Order $erpOrder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Model\ResourceModel\Order $orderResourceModel
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data              $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Order      $erpOrder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Magento\Sales\Model\ResourceModel\Order    $orderResourceModel,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder              $filterBuilder
    )
    {
        $this->helper = $helper;
        $this->erpOrder = $erpOrder;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderResourceModel = $orderResourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @param $orderId
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function syncOrdersFromMagentoToErp($cronObject, $orderId = 0) {
        $singleOrderFromGridResult = false;

        if (!$this->helper->isEnable()) {
            return;
        }

        $this->helper->log('Order Cron or From Grid: Starting to get not synced order when real time sync and manually send to ERP using Cron', 'info');

        if ($orderId) {
            $getOrder = $this->orderRepositoryInterface->get($orderId);
            $ordersToSync[0] = $getOrder;
        } else {

            $filterErpIsSync = $this->filterBuilder
                ->setField('is_synced_to_msdynamic_erp')
                ->setConditionType('null')
                ->create();

            $this->searchCriteriaBuilder->addFilters([$filterErpIsSync]);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $ordersToSync = $this->orderRepositoryInterface->getList($searchCriteria)->getItems();
        }

        if (!count($ordersToSync)) {
            $this->helper->log('Order Cron or From Grid: No not synced ordeers at the moment', 'info');
        }

        foreach ($ordersToSync as $order) {
            if ($order->getIsSyncedToMsdynamicErp()) {
                $this->helper->log('Order Cron or From Grid : This order already synced. Magento order ID' . $order->getIncrementId(), 'info');
                continue;
            }

            // Order will be sync when it is get paid total amount
            if ($order->getTotalDue()) {
                $this->helper->log('Order Cron or From Grid : This order still not fully paid. Magento order ID' . $order->getIncrementId(), 'info');
                continue;
            }

            $this->helper->log('Order Cron or From Grid : Start to process the Magento order : ' . $order->getIncrementId(), 'info');

            $orderItems = $order->getAllVisibleItems();

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

            $this->helper->log('Order Cron or From Grid : Order Total : ' . floatval($order->getGrandTotal()) . ' and Order Line Items Total :' . $orderItemTotal, 'error');

            $createOrderInErp = $this->erpOrder->createOrderInErp($this->helper->getFunctionCreateOrder(),
                $this->helper->getSoapActionCreateOrder(), $dataToOrder);

            if (empty($createOrderInErp)) {
                $this->helper->log('Order Cron or From Grid : ERP system might be off line', 'error');
                continue;
            }

            if ($createOrderInErp['curlStatus'] == 200 && isset($createOrderInErp['response']['OrderNo'])) {
                $this->helper->log('Order Cron or From Grid : Magento Order ' . $createOrderInErp['response']['OrderNo'] . ' successfully sent to ERP', 'info');

                $order->setIsSyncedToMsdynamicErp(1);
                $this->orderResourceModel->save($order);

                $singleOrderFromGridResult = true;

                $this->helper->log('Order Cron or From Grid : Successfully updated the is_synced_to_msdynamic_erp attribute for Magento Order ' . $createOrderInErp['response']['OrderNo'], 'info');

                $this->helper->log('Order Cron or From Grid : End process the Magento order : ' . $order->getIncrementId() . '. Successfully sent to ERP', 'info');

            }
        }

        if ($orderId) {
            if ($singleOrderFromGridResult) {
                return [
                    'result' => true
                ];
            }

            return [
                'result' => false
            ];
        }
    }
}
