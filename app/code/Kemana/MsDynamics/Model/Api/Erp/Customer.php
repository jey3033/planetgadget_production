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
            if (isset(json_decode($getCustomerFromErp['response'])[1])) {
                return json_decode($getCustomerFromErp['response']);
            }
        }

        return false;
    }

    /**
     * @param $apiFunction
     * @param $customerData
     * @return false|mixed
     */
    public function createCustomerInErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $soapAction, $postParameters));

        //if ($getCustomerFromErp['responseStatus']) {
        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }
        // }


        return false;
    }

    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function ackCustomer($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpAckListOfCustomers($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return false;
    }

    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function updateCustomerInErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return false;
    }

    public function getUnSyncCustomersFromErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToGetUnSyncCustomersFromApi($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return false;
    }


    /**
     * @param $apiFunction
     * @param array $customerData
     * @return false|mixed
     */
    public function deleteCustomerInErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToDeleteCustomer($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return false;
    }

}
