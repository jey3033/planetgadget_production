<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SourceDistanceShipping
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SourceDistanceShipping\Model\Api;

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
     * @var \Kemana\Crm\Helper\Data|\Kemana\SourceDistanceShipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param \Kemana\SourceDistanceShipping\Helper\Data $helper
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl          $curl,
        \Kemana\SourceDistanceShipping\Helper\Data   $helper,
        \Magento\Framework\Serialize\Serializer\Json $json
    )
    {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->json = $json;
    }

    /**
     * @param $url
     * @param string $method
     * @param $params
     * @return array|false
     */
    public function apiTransport($url, string $method = 'GET', $params = null)
    {
        $this->helper->log('Start', 'info');
        $this->helper->log('Url : ' . $url, 'info');

        try {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->setOption(CURLOPT_FOLLOWLOCATION, 1);
            $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
            $this->curl->setOption(CURLOPT_TIMEOUT, 10);

            if ($method == 'POST') {
                $params = is_array($params) ? $this->json->serialize($params) : $params;
                $this->curl->post($url, $params);
            }
            if ($method == 'GET') {
                if ($params) {
                    $url .= '?' . http_build_query($params);
                }
                $this->curl->get($url);
            }

            $response = $this->curl->getBody();

            $arrayResponse = $this->json->unserialize($response);


            if (isset($arrayResponse['error_message'])) {
                $this->helper->log('Error Response : ' . $response);

                $this->helper->log('End', 'info');

                return [
                    'responseStatus' => false,
                    'response' => $arrayResponse
                ];
            }

            $this->helper->log('Success Response : ' . $response, 'info');
            $this->helper->log('End', 'info');

            return [
                'responseStatus' => true,
                'response' => $arrayResponse
            ];

        } catch (\Exception $e) {
            $this->helper->log($e->getMessage());
            $this->helper->log('End', 'info');
        }

        return false;

    }

}
