<?php

/**
 * KS_Logistic
 *
 * @see README.md
 *
 */

namespace KS\Logistic\Model\Carrier\Sicepat;

use KS\Logistic\Model\Carrier\Sicepat;
use Magento\Shipping\Model\Tracking\ResultFactory;

/**
 * Class Api
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class Api extends \KS\Logistic\Model\Carrier\Api
{
    /**
     * @var string
     */
    protected $code = Sicepat::CODE;

    /**
     * get rate from API
     * @param string $originCode
     * @param string $destinationCode
     * @param int $weight
     * @return array
     */
    public function getApiRate($originCode, $destinationCode, $weight = 1)
    {
        $url = $this->getConfigData('url_primary');
        $path = $this->getConfigData('url_rate');
        $headers = [
            'api-key' => $this->getConfigData('api_key')
        ];

        $url .= $path;
        $params = [
            'origin' => $originCode,
            'destination' => $destinationCode,
            'weight' => $weight
        ];
        $response = $this->getApiTransport($headers, $url, 'GET', $params);

        if ($response && $result = $this->getResults($response)) {
            return $result;
        }

        return [];
    }

    /**
     * get origin
     * @return array
     */
    public function getApiOrigin()
    {
        $url = $this->getConfigData('url_primary');
        $path = $this->getConfigData('url_origin');
        $headers = [
            'api-key' => $this->getConfigData('api_key')
        ];

        $response = $this->getApiTransport($headers, $url . $path, 'GET');

        if ($response && $result = $this->getResults($response)) {
            return $result;
        }

        return [];
    }

    /**
     * get destination
     * @return array
     */
    public function getApiDestination()
    {
        $url = $this->getConfigData('url_primary');
        $path = $this->getConfigData('url_destination');
        $headers = [
            'api-key' => $this->getConfigData('api_key')
        ];

        $response = $this->getApiTransport($headers, $url . $path, 'GET');

        if ($response && $result = $this->getResults($response)) {
            return $result;
        }

        return [];
    }

    /**
     * send pickup request
     * @param Magento\Sales\Model\Order\Shipment $shipment
     * @return void
     */
    public function sendPickupRequest($shipment)
    {
        $requestMode = $this->getConfigData('api_development_mode');
        $url = $requestMode ? $this->getConfigData('url_pickup_development') : $this->getConfigData('url_pickup');
        $path = $this->getConfigData('url_pickpath');
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $params = $this->buildPickupParams($shipment);

        if (!count($params['allowed_packages'])) {
            return;
        }

        $response = $this->getApiTransport($headers, $url . $path, 'POST', $this->json->serialize($params['body']));

        if ($response) {
            $result = $this->json->unserialize($response);
            if (isset($result['request_number']) && $result['request_number']
                && isset($result['datas']) && $result['datas']
            ) {
                $this->updateRegisterStatus($shipment, $params['allowed_packages']);
                foreach ($result['datas'] as $value) {
                    return ['reference' => $result['request_number'], 'receipt' => $value['receipt_number']];
                }
            }
        }
    }

    /**
     * Build API request body
     *
     * @param Magento\Sales\Model\Order\Shipment $shipment
     * @return array
     */
    protected function buildPickupParams($shipment)
    {
        $pickupAddress = $this->getShipmentSource($shipment);
        $districtOrigin = $this->districtRepo->getDistrict($pickupAddress['district_id']);
        $pickupAddress['origin_code'] = $districtOrigin->getSicepatShippingOrigin();
        $allowedPackages = $this->getAllowedPackages($shipment);

        $body = [
            "auth_key" => $this->getConfigData('pickup_api_key'),
            "pickup_method" => 'PICKUP',
            "pickup_merchant_code" => $this->getConfigData('merchant_code'),
            "pickup_merchant_name" => $this->getConfigData('merchant_name'),
            "pickup_address" => $pickupAddress['full_address'],
            "pickup_city" => $pickupAddress['city'],
            "pickup_merchant_phone" => $pickupAddress['telephone'],
            "pickup_merchant_email" => $pickupAddress['email'],
            "reference_number" => $shipment->getIncrementId(),
            "pickup_request_date" => $this->getPickupDate(),
            "PackageList" => $this->getPackageList($shipment, $pickupAddress, $allowedPackages)
        ];

        return [
            'allowed_packages' => $allowedPackages,
            'body' => $body
        ];
    }

    /**
     * Retrieve items from package
     * @param Magento\Sales\Model\Order\Shipment $shipment
     * @param array $origin
     * @param array $allowedPackages
     * @return array
     */
    protected function getPackageList($shipment, $origin, $allowedPackages)
    {
        $packageList = [];
        $order = $shipment->getOrder();
        $address = $order->getShippingAddress();
        $destinationPoint = explode(",", $address->getData('map_point'));
        $sourcePoint = explode(",", $origin['map_point']);
        $district = $this->districtRepo->getDistrict($address->getData('district_id'));

        $packages = $shipment->getPackages();

        foreach ($packages as $packageId => $package) {
            if (!in_array($packageId, $allowedPackages)) {
                continue;
            }
            $packageList[] = [
                "receipt_number" => $this->getTrackNumberByPackage($shipment, $packageId, $package),
                "origin_code" => $origin['origin_code'],
                "delivery_type" => $this->getServiceName($order->getShippingMethod()),
                "parcel_category" => "Normal",
                "parcel_content" => $this->getPackageDesc($package),
                "parcel_qty" => 1,
                "parcel_uom" => $package['params']['weight_units'],
                "parcel_value" => number_format($package['params']['customs_value'], 0, '.', ''),
                'cod_value' => 0,
                'insurance_value' => 0,
                "total_weight" => (int)$package['params']['weight'],
                "shipper_name" => $origin['contact'],
                "shipper_address" => $origin['full_address'],
                "shipper_province" => $origin['full_address'],
                "shipper_city" => $origin['city'],
                "shipper_district" => $origin['district'],
                "shipper_zip" => $origin['postcode'],
                "shipper_phone" => $origin['telephone'],
                "recipient_title" => " ",
                "recipient_name" => $address->getName(),
                "recipient_address" => implode(" ", $address->getStreet()),
                "recipient_province" => $address->getRegion(),
                "recipient_city" => $address->getCity(),
                "recipient_district" => $address->getDistrict(),
                "recipient_zip" => $address->getPostcode(),
                "recipient_phone" => $address->getTelephone(),
                "destination_code" => $district->getSicepatDistrictCode(),
                "recipient_latitude" => $destinationPoint[0] ?? null,
                "recipient_longitude" => $destinationPoint[1] ?? null,
                "shipper_longitude" => $sourcePoint[1] ?? null,
                "shipper_latitude" => $sourcePoint[0] ?? null
            ];
        }

        return $packageList;
    }

    /**
     * {@inheritedoc}
     */
    public function generatePreBookingAwb($params)
    {
        foreach (range($params['range_from'], $params['range_to']) as $number) {
            $this->awbRepository->create($this->code, $number);
        }
    }

    /**
     * Get tracking
     *
     * @param string[] $trackings
     * @return ResultFactory
     */
    public function track($trackings)
    {
        //so we can only reply the popup window to ups.
        $result = $this->trackFactory->create();
        foreach ($trackings as $tracking) {
            $summary = $this->getTrackingContent($tracking);


            $status = $this->trackStatusFactory->create();
            $status->setCarrier($this->code);
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->addData($summary);
            $result->append($status);
        }

        return $result;
    }

    /**
     * get tracking content
     * @param string $trackNumber
     * @return array
     */
    protected function getTrackingContent($trackNumber)
    {
        $status = 'PROCESSING';
        $response = $this->getApiTracking($trackNumber);
        $progress = [];
        $progress['status'] = $status;

        if ($response) {
            if (isset($response["service"])) {
                $progress['service'] = $response["service"];
            }
            if (isset($response["send_date"])) {
                $progress['shipped_date'] = $response["send_date"];
            }
            if (isset($response["weight"])) {
                $progress['weight'] = $response["weight"];
            }
            if (isset($response["last_status"])) {
                $status = isset($response["last_status"]["status"]) ? $response["last_status"]["status"] : $status;
                $progress['status'] = $status;
                $progress['delivery_date'] = $status == 'DELIVERED' && isset($response["last_status"]["date_time"]) ? $response["last_status"]["date_time"] : null;
            }

            $sender = [];
            if (isset($response["sender"])) {
                $sender[] = $response["sender"];
            }

            if (isset($response["sender_address"])) {
                $sender[] = $response["sender_address"];
            }

            $progress['origin_location'] = implode(" - ", $sender);

            $receiver = [];
            if (isset($response["receiver_name"])) {
                $receiver[] = $response["receiver_name"];
            }

            if (isset($response["receiver_address"])) {
                $receiver[] = $response["receiver_address"];
            }
            $progress['delivery_location'] = implode(" - ", $receiver);


            if (isset($response["track_history"]) && count($response["track_history"])) {
                $manifests = $response["track_history"];
                $progress['progressdetail'] = [];
                foreach ($manifests as $manifest) {
                    $time = explode(" ", $manifest["date_time"]);

                    $activity = null;
                    if (isset($manifest["city"])) {
                        $activity = $manifest["city"];
                    }
                    if (isset($manifest["receiver_name"])) {
                        $activity = $manifest["receiver_name"];
                    }

                    $progress['progressdetail'][] = [
                        'deliverylocation' => $manifest["status"],
                        'deliverydate' => isset($time[0]) ? $time[0] : null,
                        'deliverytime' => isset($time[0]) ? $time[1] : null,
                        'activity' => $activity
                    ];
                }
            }
        }

        return $progress;
    }

    /**
     * get api tracking
     * @param string $trackNumber
     * @return string|void
     */
    protected function getApiTracking($trackNumber)
    {
        $url = $this->getConfigData('url_primary');
        $path = $this->getConfigData('url_tracking');
        $headers = [
            'api-key' => $this->getConfigData('api_key')
        ];

        $params = [
            'waybill' => $trackNumber
        ];

        $response = $this->getApiTransport($headers, $url . $path, 'GET', $params);

        if ($response && $result = $this->getResults($response)) {
            return $result;
        }
    }

    /**
     * get results
     * @param string $response
     * @return array|string
     */
    protected function getResults($response)
    {
        $response = $this->json->unserialize($response);
        if (isset($response['sicepat']['status']['code']) && $response['sicepat']['status']['code'] == '400') {
            return false;
        }
        if (isset($response['sicepat']['results']) && $response['sicepat']['results']) {
            return $response['sicepat']['results'];
        }
        if (isset($response['sicepat']['result']) && $response['sicepat']['result']) {
            return $response['sicepat']['result'];
        }
    }
}
