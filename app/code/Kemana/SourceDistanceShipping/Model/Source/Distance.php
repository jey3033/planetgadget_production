<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SourceDistanceShipping
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SourceDistanceShipping\Model\Source;

/**
 * Class Distance
 */
class Distance
{
    /**
     * @var \Kemana\SourceDistanceShipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\SourceDistanceShipping\Model\Api\Request
     */
    protected $request;

    /**
     * @param \Kemana\SourceDistanceShipping\Helper\Data $helper
     * @param \Kemana\SourceDistanceShipping\Model\Api\Request $request
     */
    public function __construct(
        \Kemana\SourceDistanceShipping\Helper\Data       $helper,
        \Kemana\SourceDistanceShipping\Model\Api\Request $request
    )
    {
        $this->request = $request;
        $this->helper = $helper;

    }

    /**
     * Main function which check the shipping method can apply or not based on the two string addresses
     *
     * @param $customerShippingAddress
     * @param $sourceLocationsAddress
     * @param $distanceFromMethod
     * @return bool
     */
    public function checkDistanceFromShippingAddressToSourceLocation($customerShippingAddress, $sourceLocationsAddress, $distanceFromMethod): bool
    {

        $distanceBetweenTwoAddressesMeters = $this->getDistanceBetweenTwoAddresses($customerShippingAddress, $sourceLocationsAddress);

        if ($distanceBetweenTwoAddressesMeters) {

            $distanceBetweenTwoAddressesKm = $distanceBetweenTwoAddressesMeters / 1000;

            if ($distanceFromMethod > $distanceBetweenTwoAddressesKm) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get distance of two addresses using Google Api
     *
     * @param $address1
     * @param $address2
     * @return false|mixed
     */
    public function getDistanceBetweenTwoAddresses($address1, $address2)
    {
        if (!$address1 || !$address2) {
            return false;
        }

        $address1 = str_replace(' ', '+', $address1);
        $address2 = str_replace(' ', '+', $address2);

        $googleKey = $this->helper->getGoogleApiKey();
        $requestUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $address1 . "&destinations=" . $address2 . "&travelMode=driving&sensor=false&key=" . $googleKey;

        $response = $this->request->apiTransport($requestUrl);

        // Check the response element one by one and access correct value
        if ($response && $response['responseStatus'] && (isset($response['response']['status']) && $response['response']['status'] == 'OK')) {

            if (isset($response['response']['rows'][0]['elements'][0]['distance']['value'])) {
                return $response['response']['rows'][0]['elements'][0]['distance']['value'];
            }

        } else {
            $this->helper->log('Failed when getting distance between Address 1 : ' . $address1 . ' and Address 2 :' . $address2, 'error');
        }

        return false;
    }

    /**
     * @TODO not using at the moment but keep the functions for future use
     * Get longitude and latitude using string address
     *
     * @param $address
     * @return array|false|string[]
     */
    public function getLongitudeLatitude($address)
    {
        if (!$address) {
            return [];
        }

        $address = str_replace(' ', '+', $address);
        $googleKey = $this->helper->getGoogleApiKey();
        $requestUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&sensor=false&key=" . $googleKey;

        $response = $this->request->apiTransport($requestUrl);

        if ($response && $response['responseStatus'] && (isset($response['response']['status']) && $response['response']['status'] == 'OK')) {

            if (isset($response['response']['results']) && isset($response['response']['results'][0]) &&
                isset($response['response']['results'][0]['geometry']) && $response['response']['results'][0]['geometry']['location']
            ) {
                return [
                    'lon' => $response['response']['results'][0]['geometry']['location']['lng'],
                    'lat' => $response['response']['results'][0]['geometry']['location']['lat']
                ];
            }

        } else {
            $this->helper->log('Failed when getting lon or lat for shipping or source location address : ' . $address, 'error');
        }

        return false;

    }

    /**
     * @TODO not using at the moment but keep the functions for future use
     * Calculate distance between two locations using latitude and langitude
     *
     * @param $fromLonLat
     * @param $toLonLat
     * @param $unit
     * @return false|float
     */
    public function getDistanceBetweenLongLat($fromLonLat, $toLonLat, $unit = 'K')
    {
        if (!isset($fromLonLat['lon']) || !isset($fromLonLat['lat']) || !isset($toLonLat['lon']) || !isset($toLonLat['lat'])) {
            return false;
        }

        $theta = $fromLonLat['lon'] - $toLonLat['lon'];
        $dist = sin(deg2rad($fromLonLat['lat'])) * sin(deg2rad($toLonLat['lat'])) + cos(deg2rad($fromLonLat['lat'])) * cos(deg2rad($toLonLat['lat'])) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        if ($unit == 'K') {
            return round($miles * 1.609344, 2);
        } else {
            return $miles;
        }

    }

}


