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
 * @author   Tushar Korat <tushar@kemana.com>
 */

namespace Kemana\MsDynamics\Logger;

/**
 * Class ApiTransportLog
 */
class ApiTransportLogger
{
    /**
     * @var \Kemana\MsDynamics\Model\ApiTransportLogFactory
     */
    protected $apiTransportLogFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Constructor for ApiTransportLogger
     *
     * @param \Kemana\MsDynamics\Model\ApiTransportLogFactory $apiTransportLogFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Kemana\MsDynamics\Model\ApiTransportLogFactory $apiTransportLogFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->apiTransportLogFactory = $apiTransportLogFactory;
        $this->logger = $logger;
    }

    /**
     * Save Api Transport data to DB Table
     *
     * @param string $url
     * @param string $method
     * @param string $apiFunction
     * @param string $soapAction
     * @param xml $requestBody
     * @param xml $responseBody
     * @param string $responseStatus
     * @return $this
     */
    public function saveApiTransportLogToDB($url, $method, $apiFunction,
        $soapAction, $requestBody, $responseBody, $responseStatus)
    {
        try {
            $status = 0;
            if ($responseStatus == '200' || $responseStatus == '100') {
                $status = 1;
            }

            $type = '';
            if (strpos($apiFunction, "customer") !== false) {
                $type = __('CUSTOMER');
            } else if (strpos($apiFunction, "product") !== false) {
                $type = __('PRODUCT');
            } else if (strpos($apiFunction, "inventory") !== false) {
                $type = __('INVENTORY');
            } else if (strpos($apiFunction, "rewardpoint") !== false) {
                $type = __('REWARDPOINT');
            } else {
                $type = $apiFunction;
            }

            $model = $this->apiTransportLogFactory->create();
            $collection = $model->getCollection();
            $collection->addFieldToFilter('request_body', ['eq' => $requestBody]);
            $collection->getFirstItem();
            if($collection->getData()){
                foreach ($collection as $_item) {
                    $_item->setType($type);
                    $_item->setUrl($url);
                    $_item->setMethod($method);
                    $_item->setApiFunction($apiFunction);
                    $_item->setSoapAction($soapAction);
                    $_item->setRequestBody($requestBody);
                    $_item->setResponseBody($responseBody);
                    $_item->setResponseStatus($responseStatus);
                    $_item->setStatus($status);
                    $_item->save();
                }
            } else {
                $model->addData([
                    "type" => $type,
                    "url" => $url,
                    "method" => $method,
                    "api_function" => $apiFunction,
                    "soap_action" => $soapAction,
                    "request_body" => $requestBody,
                    "response_body" => $responseBody,
                    "response_status" => $responseStatus,
                    "status" => $status
                    ]);
                $model->save();
            }
        } catch (\Exception $e) {
           $this->logger->critical('Error message', ['exception' => $e]);
        }
    }
}
