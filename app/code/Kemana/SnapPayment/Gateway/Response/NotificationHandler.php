<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SnapPayment
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SnapPayment\Gateway\Response;

use Kemana\Snap\Gateway\Config\Config;
use Kemana\Snap\Logger\Logger;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Kemana\SnapPayment\Logger\Logger as SnapPaymentLogger;

/**
 * Class NotificationHandler
 */
class NotificationHandler extends \Kemana\Snap\Gateway\Response\NotificationHandler
{
    /**
     * @var SnapPaymentLogger
     */
    protected $snapLogger;

    /**
     * @param Registry $registry
     * @param Config $config
     * @param Logger $logger
     * @param Order $order
     * @param SnapPaymentLogger $snapLogger
     */
    public function __construct(Registry $registry, Config $config, Logger $logger, Order $order, SnapPaymentLogger $snapLogger)
    {
        $this->snapLogger = $snapLogger;
        parent::__construct($registry, $config, $logger, $order);
    }

    /**
     * @return DataObject|null
     */
    public function getNotification()
    {
        $this->snapLogger->info('Reached the function getNotification()');
        $rawInput = file_get_contents('php://input');
        $rawData  = @json_decode($rawInput, true);

        $this->snapLogger->info('Request Body JSON Content : '. $rawInput);

        if (!$rawInput || empty($rawData) || !is_array($rawData)) {
            $this->snapLogger->info('Request Body empty and returned null');
            return null;
        }

        if (!isset($rawData['signature_key']) || !$this->validateSignature($rawData)) {
            $this->snapLogger->info('Signature key missing or invalid key and returned null');
            return null;
        }

        $this->snapLogger->info('All good in getNotification()');

        return $this->handle($rawData);
    }

}
