<?php

namespace Kemana\Logistics\Model\Carrier\Jne;

class Jne extends \KS\Logistic\Model\Carrier\Jne\Api
{
    /**
     * Get Request No
     * @param Magento\Shipping\Model\Shipment\Request $request
     * @return [type] [description]
     */
    protected function getAwbRequestNo($request)
    {
        $shipment = $request->getOrderShipment();
        return $this->getIncrement($shipment) . 'P' . $request->getPackageId() . '/AWB/LWA/PG';
    }
}
