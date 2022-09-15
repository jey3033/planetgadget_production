<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api\Erp;

/**
 * Class Order
 */
class Order
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
     * @param $orderData
     * @return array
     */
    public function createOrderInErp($apiFunction, $soapAction, $orderData)
    {
        $postParameters = $orderData;

        $createOrderInErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErp($apiFunction, $soapAction, $postParameters));

        if (isset($createOrderInErp['response'])) {
            return $createOrderInErp;
        }

        return [];
    }
}
