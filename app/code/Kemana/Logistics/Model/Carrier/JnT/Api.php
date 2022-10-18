<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Logistics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */
namespace Kemana\Logistics\Model\Carrier\JnT;

use Magento\Store\Model\ScopeInterface;

class Api extends \KS\Logistic\Model\Carrier\JnT\Api
{
    /**
     * get awb from API
     * @param Magento\Shipping\Model\Shipment\Request $request
     * @return array
     */
    protected function getApiAirWayBill($request)
    {
        $shipment = $request->getOrderShipment();
        $packageParams = $request->getPackageParams();
        $origin = $this->getShipmentSource($shipment);
        $address = $shipment->getOrder()->getShippingAddress();
        $destDistrict = $this->districtRepo->getDistrict($address->getDistrictId());
        $order = $shipment->getOrder();
        $url = $this->getConfigData('base_url');
        $path = $this->getConfigData('url_order');
        $salt = $this->getConfigData('order_key');

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $pickupTime = $this->getPickupDate();
        $pickupTime = date("Y-m-d h:m:s", strtotime($pickupTime));
        $item_name = [];
        foreach ($shipment->getItemsCollection() as $item) {
                array_push($item_name,$item['name']);
        }

        $params = [
            'username' => $this->getConfigData('username'),
            'api_key' => $this->getConfigData('api_key'),
            'orderid' => $request->getAwbString(),
            'shipper_name' => substr($this->scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE), 0, 30),
            'shipper_contact' => substr($origin['contact'], 0, 30),
            'shipper_phone' => $origin['telephone'],
            'shipper_addr' => substr($origin['full_address'], 0, 200),
            'origin_code' => $origin['origin_code'],
            'receiver_name' => substr($address->getName(), 0, 30),
            'receiver_addr' => $this->formatAddress($address),
            'receiver_phone' => $address->getTelephone(),
            'receiver_zip' => $address->getPostcode(),
            'destination_code' => $destDistrict->getJntShippingOrigin(),
            'receiver_area' => $destDistrict->getJntDistrictCode(),
            'qty' => 1,
            'weight' => $packageParams->getWeight(),
            'goodsdesc' => substr($this->getItemDescription($request), 0, 40),
            'servicetype' => 1,
            'insurance' => substr((int) $order->getInsuranceFee(), 0, 8),
            'orderdate' => $order->getCreatedAt(),
            'item_name' => substr(implode(",",$item_name), 0, 50),
            'cod' => null,
            'sendstarttime' => $pickupTime,
            'sendendtime' => $this->lastPickTime($pickupTime),
            'expresstype' => 1,
            'goodsvalue' => substr((int) $order->getGrandTotal(), 0, 8)
        ];

        $params = ['detail' => [$params]];

        $response = $this->getApiTransport($headers, $url . $path, 'POST', [
            'data_param' => $this->json->serialize($params),
            'data_sign' => $this->getSignature($params, $salt)
        ]);

        if ($response && $result = $this->getResults($response)) {
            if (isset($result['detail'][0]['awb_no'])) {
                return $result['detail'][0]['awb_no'];
            }
        }
    }
}
