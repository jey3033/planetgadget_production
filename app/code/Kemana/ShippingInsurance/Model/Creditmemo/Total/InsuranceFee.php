<?php /** @noinspection PhpUndefinedMethodInspection */

/**
 * Copyright © 2017 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */
/**
 * @category Kemana
 * @package  Kemana_ShippingInsurance
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anupam Tiwari<anupam.tiwari@kemana.com>, Cipto Raharjo<craharjo@kemana.com>
 */

namespace Kemana\ShippingInsurance\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Kemana\ShippingInsurance\Helper\Data as InsuranceFeeHelper;

/**
 * Class InsuranceFee
 * @package Kemana\ShippingInsurance\Model\Creditmemo\Total
 */
class InsuranceFee extends AbstractTotal
{
    /**
     * @var InsuranceFeeHelper
     */
    protected $helper;

    /**
     * InsuranceFee constructor.
     *
     * @param InsuranceFeeHelper $helper
     * @param array $data
     */
    public function __construct(InsuranceFeeHelper $helper, array $data = [])
    {
        parent::__construct($data);
        $this->helper = $helper;
    }

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $creditmemo->setInsuranceFee(0);
        $creditmemo->setBaseInsuranceFee(0);
        if (!$this->helper->isRefund()) {
            return $this;
        }
        $order = $creditmemo->getOrder();
        $isRefund = true;
        $totalRefunded = $order->getTotalRefunded() + $creditmemo->getGrandTotal() + $creditmemo->getShippingAmount() + $order->getInsuranceFee() + $order->getProtectiveBoxFee();
        if ($order->getTotalPaid() > $totalRefunded) {
            $isRefund = false;
        }
        if (!$isRefund) {
            $amount = 0;
            $baseAmount = 0;
        } else {
            $amount = $order->getInsuranceFee();
            $baseAmount = $order->getBaseInsuranceFee();
        }
        
        $creditmemo->setInsuranceFee($amount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $amount);

        
        $creditmemo->setBaseInsuranceFee($baseAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseAmount);

        return $this;
    }
}
