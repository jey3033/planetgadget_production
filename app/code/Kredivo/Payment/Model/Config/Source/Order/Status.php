<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kredivo\Payment\Model\Config\Source\Order;

use Magento\Sales\Model\Config\Source\Order\Status as BaseStatus;
use Magento\Sales\Model\Order;

/**
 * Order Status source model
 */
class Status extends BaseStatus
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [
        \Magento\Sales\Model\Order::STATE_NEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING,
        \Magento\Sales\Model\Order::STATE_COMPLETE,
        \Magento\Sales\Model\Order::STATE_CLOSED,
        \Magento\Sales\Model\Order::STATE_CANCELED,
        \Magento\Sales\Model\Order::STATE_HOLDED,
    ];
}
