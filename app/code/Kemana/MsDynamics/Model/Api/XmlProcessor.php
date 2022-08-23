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
 * Class XmlProcessor
 */
class XmlProcessor
{
    /**
     * @var \Magento\Framework\Convert\Xml
     */
    protected $convertXml;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $xmlParser;

    /**
     * @param \Magento\Framework\Convert\Xml $convertXml
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\Xml\Parser $xmlParser
     */
    public function __construct(
        \Magento\Framework\Convert\Xml               $convertXml,
        \Kemana\MsDynamics\Helper\Data               $helper,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Xml\Parser                $xmlParser
    )
    {
        $this->convertXml = $convertXml;
        $this->helper = $helper;
        $this->json = $json;
        $this->xmlParser = $xmlParser;
    }

    /**
     * @param $xmlResponseBody
     * @param $responseStatus
     * @param $apiFunction
     * @return array|mixed
     */
    public function processResponse($xmlResponseBody, $responseStatus, $apiFunction)
    {
        if (!is_string($xmlResponseBody)) {
            throw new \InvalidArgumentException(sprintf('"%s" data type is invalid. String is expected.', gettype($xmlResponseBody)));
        }

        try {
            $this->xmlParser->loadXML($xmlResponseBody);
            $responseData = $this->xmlParser->xmlToArray();
            $responseData = reset($responseData);
        } catch (\Exception $e) {
            $this->helper->log('Error when loading XML Response from API :' . $e->getMessage());
        }

        if ($responseStatus == "200") {
            if (isset($responseData['Soap:Body'])) {
                if (isset($responseData['Soap:Body'][$apiFunction . '_Result']['return_value'])) {
                    return $responseData['Soap:Body'][$apiFunction . '_Result']['return_value'];
                }
            }
        }

        return [];
    }

}
