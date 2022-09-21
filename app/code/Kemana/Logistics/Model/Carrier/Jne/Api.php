<?php

namespace Kemana\Logistics\Model\Carrier\Jne;

class Api extends \KS\Logistic\Model\Carrier\Jne\Api
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

    /**
     * Build API request body
     *
     * @param Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    protected function buildBaseShipmentParams($shipment)
    {
        if ($this->requestBody) {
            return $this->requestBody;
        }

        $pickupAddress = $this->getShipmentSource($shipment);
        $districtOrigin = $this->districtRepo->getDistrict($pickupAddress['district_id']);
        $pickupAddress['origin_code'] = $districtOrigin->getSicepatShippingOrigin();
        $originStreet = str_split($pickupAddress['street1'], 30);

        $order = $shipment->getOrder();
        $address = $order->getShippingAddress();
        $street = str_split(implode(" ", $address->getStreet()), 30);
        $destDistrict = $this->districtRepo->getDistrict($address->getData('district_id'));

        $this->requestBody = [
            'api_key' => $this->getConfigData('api_key'),
            'username' => $this->getConfigData('api_username'),
            'OLSHOP_BRANCH' => $this->getBranchCode($districtOrigin->getDistrictCode()),
            'OLSHOP_CUST' => $this->getConfigData('api_merchant_id'),
            'OLSHOP_ORIG' => $districtOrigin->getDistrictCode(),
            'OLSHOP_ORDERID' => $shipment->getIncrementId(),
            'OLSHOP_SHIPPER_NAME' => $this->getConfigData('api_merchant_name'),
            'OLSHOP_SHIPPER_ADDR1' => isset($originStreet[0]) ? $originStreet[0] : ' ',
            'OLSHOP_SHIPPER_ADDR2' => isset($originStreet[1]) ? $originStreet[1] : ' ',
            'OLSHOP_SHIPPER_ADDR3' => isset($originStreet[3]) ? $originStreet[2] : ' ',
            'OLSHOP_SHIPPER_CITY' => substr($pickupAddress['city'], 0, 20),
            'OLSHOP_SHIPPER_REGION' => substr($pickupAddress['region'], 0, 20),
            'OLSHOP_SHIPPER_ZIP' => $pickupAddress['postcode'],
            'OLSHOP_SHIPPER_PHONE' => $pickupAddress['telephone'],
            'OLSHOP_RECEIVER_NAME' => substr($address->getName(), 0, 30),
            'OLSHOP_RECEIVER_ADDR1' => isset($street[0]) ? $street[0] : ' ',
            'OLSHOP_RECEIVER_ADDR2' => isset($street[1]) ? $street[1] : ' ',
            'OLSHOP_RECEIVER_ADDR3' => isset($street[2]) ? $street[2] : ' ',
            'OLSHOP_RECEIVER_CITY' => substr($address->getCity(), 0, 20),
            'OLSHOP_RECEIVER_REGION' => substr($address->getRegion(), 0, 20),
            'OLSHOP_RECEIVER_ZIP' => !empty($address->getPostcode()) ? $address->getPostcode() : '00000',
            'OLSHOP_RECEIVER_PHONE' => $address->getTelephone(),
            'OLSHOP_DEST' => $destDistrict->getDistrictCode(),
            'OLSHOP_SERVICE' => $this->getServiceName($order->getShippingMethod()),
            'OLSHOP_GOODSTYPE' => 2,
            'OLSHOP_GOODSDESC' => "NA",
            'OLSHOP_INST' => "",
            'OLSHOP_INS_FLAG' => "N",
            'OLSHOP_COD_FLAG' => "NO",
            'OLSHOP_COD_AMOUNT' => 0
        ];

        if($order->getInsuranceFee() > 0){
            $this->requestBody['OLSHOP_INS_FLAG'] = 'Y';
        }
        
        return $this->requestBody;
    }
}
