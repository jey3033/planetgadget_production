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
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api\Erp;

/**
 * Class Customer
 */
class Customer
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
     * @param $customerIdInErp
     * @return false|mixed
     */
    public function getCustomerInErp($apiFunction, $customerIdInErp = '0')
    {
        $postParameters = "";

        if ($customerIdInErp) {
            $postParameters = '<customer_no>' . $customerIdInErp . '</customer_no>';
        }

        $getCustomerFromErp = $this->request->apiTransport($apiFunction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $postParameters));

        if ($getCustomerFromErp['responseStatus']) {
            if (isset(json_decode($getCustomerFromErp['response'])[1][0])) {
                return json_decode($getCustomerFromErp['response'])[1][0];
            }
        }

        return false;
    }

    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function createCustomerInErp($apiFunction, array $customerData = [])
    {
        $postParameters = '<json>[' . json_encode($customerData) . ']</json>';

        $getCustomerFromErp = $this->request->apiTransport($apiFunction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $postParameters));

        if ($getCustomerFromErp['responseStatus']) {
            if (isset(json_decode($getCustomerFromErp['response'])[1])) {
                return json_decode($getCustomerFromErp['response'])[1];
            }
        }

        return false;
    }

    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function ackCustomer($apiFunction, array $customerData = [])
    {
        $postParameters = '<json>[' . json_encode($customerData) . ']</json>';

        $getCustomerFromErp = $this->request->apiTransport($apiFunction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $postParameters));

        if ($getCustomerFromErp['responseStatus']) {
            if (isset(json_decode($getCustomerFromErp['response'])[1])) {
                return json_decode($getCustomerFromErp['response'])[1];
            }
        }
        return false;
    }

    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function updateCustomerInErp($apiFunction, array $customerData = [])
    {
        $postParameters = '<json>[' . json_encode($customerData) . ']</json>';

        $getCustomerFromErp = $this->request->apiTransport($apiFunction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $postParameters));

        if ($getCustomerFromErp['responseStatus']) {
            if (isset(json_decode($getCustomerFromErp['response'])[1])) {
                return json_decode($getCustomerFromErp['response'])[1];
            }
        }
        return false;
    }

}
