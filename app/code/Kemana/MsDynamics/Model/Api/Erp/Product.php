<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api\Erp;

/**
 * Class Product
 */
class Product
{
    /**
     * @var \Kemana\MsDynamics\Model\Api\Request
     */
    protected $request;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\MsDynamics\Model\Api\Request $request
     * @param \Kemana\MsDynamics\Helper\Data $helper
     */
    public function __construct(
        \Kemana\MsDynamics\Model\Api\Request $request,
        \Kemana\MsDynamics\Helper\Data       $helper
    )
    {
        $this->request = $request;
        $this->helper = $helper;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @return false|mixed
     */
    public function ackProduct($apiFunction, $soapAction, $productData)
    {
        $postParameters = $productData;

        $getProductFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpAckListOfProducts($apiFunction, $soapAction, $postParameters));

        if (isset($getProductFromErp['response'])) {
            return $getProductFromErp;
        }

        return false;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @return false|mixed
     */
    public function getUnSyncProductsFromErp($apiFunction, $soapAction, $productData)
    {
        $postParameters = $productData;

        $getProductFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToGetUnSyncProductsFromApi($apiFunction, $soapAction, $postParameters));

        if (isset($getProductFromErp['response'])) {
            return $getProductFromErp;
        }

        return false;
    }

}
