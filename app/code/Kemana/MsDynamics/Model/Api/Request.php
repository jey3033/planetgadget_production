<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api;

/**
 * Class Request
 */
class Request
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var XmlProcessor
     */
    protected $xmlProcessor;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param XmlProcessor $xmlProcessor
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl          $curl,
        \Kemana\MsDynamics\Helper\Data               $helper,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Kemana\MsDynamics\Model\Api\XmlProcessor    $xmlProcessor
    )
    {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->json = $json;
        $this->xmlProcessor = $xmlProcessor;
    }

    /**
     * @param string $apiFunction
     * @param $postParameters
     * @param string $method
     * @return array|false
     */
    public function apiTransport(string $apiFunction, string $soapAction, $postParameters, string $method = 'POST')
    {
        $apiUrl = $this->helper->getApiUrl().'/'.$apiFunction;

        $this->helper->log('Start API Call : ' . $apiFunction, 'info');
        $this->helper->log('Url : ' . $apiUrl, 'info');
        $this->helper->log('Request Body : ' . $postParameters, 'info');

        if (!$apiFunction) {
            $this->helper->log('The ERP API function is missing for the API call.');

            return false;
        }

        try {
            $this->curl->setHeaders([
                'Content-Type' => 'application/xml',
                'SoapAction' => $soapAction
            ]);

            $this->curl->setOption(CURLOPT_TIMEOUT, 50000);
            $this->curl->setOption(CURLOPT_FOLLOWLOCATION, 1);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
            $this->curl->setOption(CURLOPT_TIMEOUT, 10);

            // Basic Authorization
            $this->curl->setCredentials($this->helper->getApiUsername(), $this->helper->getApiPassword());

            if ($method == 'POST') {
                $this->curl->setOption(CURLOPT_POSTFIELDS, $postParameters);

                $this->curl->post($apiUrl, []);
            }

            $responseStatus = $this->curl->getStatus();
            $xmlResponseBody = $this->curl->getBody();

            $precessedResponse = $this->xmlProcessor->processResponse($this->curl->getBody(), $responseStatus, $apiFunction, $soapAction);

            if ($responseStatus == '500') {
                $this->helper->log('Error Response : ' . $xmlResponseBody);
                $this->helper->log('End API Call : ' . $apiFunction, 'info');

                return [
                    'curlStatus' => $responseStatus,
                    'responseStatus' => false,
                    'response' => $precessedResponse
                ];
            }

            if ($responseStatus == '200') {
                $this->helper->log('Success Response : ' . $xmlResponseBody);
                $this->helper->log('End API Call : ' . $apiFunction, 'info');

                return [
                    'curlStatus' => $responseStatus,
                    'responseStatus' => true,
                    'response' => $precessedResponse
                ];
            }

        } catch (\Exception $e) {
            $this->helper->log($e->getMessage());

            $this->helper->log('Error Response : ' . $e->getMessage());
            $this->helper->log('End API Call : ' . $apiFunction, 'info');

            return false;
        }

        $this->helper->log('Error Response end of the app/code/Kemana/MsDynamics/Model/Api/Request.php');
        $this->helper->log('End API Call : ' . $apiFunction, 'info');

        return false;

    }

}
