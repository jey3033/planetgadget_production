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

namespace Kemana\MsDynamics\Model\Config;

/**
 * Class ConfigProvider
 */
class ConfigProvider
{
    /**
     * XML Path is enabled integrations
     */
    const XML_PATH_IS_ENABLE = 'msdynamic/general/is_enabled';

    /**
     * XML Path API Url
     */
    const XML_PATH_API_URL = 'msdynamic/general/url';

    /**
     * XML Path API Usernama
     */
    const XML_PATH_API_USERNAME = 'msdynamic/general/username';

    /**
     * XML Path API Password
     */
    const XMP_PATH_API_PASSWORD = 'msdynamic/general/password';

    /**
     * XML Path XMLNS
     */
    const XML_PATH_API_XMLNS = 'msdynamic/general/xmlns';

    /**
     * XML Path is enabled log
     */
    const XMP_PATH_IS_LOG_ENABLE = 'msdynamic/general/enable_log';

    /**
     * XML Path API function name insert new customer
     */
    const CREATE_CUSTOMER_IN_ERP = 'InsertCustomer';

    /**
     * XML Path API function name get customer list
     */
    const GET_CUSTOMER_LIST_IN_ERP = 'GetCustomers';

    /**
     * XML Path API function name get single customer
     */
    const GET_CUSTOMER_IN_ERP = 'GetCustomer';

    /**
     * XML Path API function name Ack customer
     */
    const ACK_CUSTOMER_IN_ERP = 'AckCustomer';

    /**
     * XML Path API function name update customer
     */
    const UPDATE_CUSTOMER_IN_ERP = 'UpdateCustomer';

    /**
     * XML Path API function name delete customer
     */
    const DELETE_CUSTOMER_IN_ERP = 'BlockCustomer';

    /**
     * Main errors from the API
     */
    const ERP_RESPONSES = [
        '1' => 'Success',
        '200' => 'Unable parsing data',
        '800' => 'Item not found',
        '801' => 'Item already exist',
        '900' => 'Customer not found',
        '901' => 'Customer already exist',
        '999' => 'Customer ack – update failed',
    ];
}
