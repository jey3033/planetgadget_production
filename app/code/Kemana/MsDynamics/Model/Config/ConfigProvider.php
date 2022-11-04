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
     * XML Path is enabled integrations
     */
    const XML_PATH_API_MODE = 'msdynamic/general/api_mode';

    /**
     * XML Path API Url
     */
    const XML_PATH_API_URL = 'msdynamic/general/url';

    /**
     * XML path API URL for Delete
     */
    const XML_PATH_API_URL_FOR_DELETE = 'msdynamic/general/url_for_delete';

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
     * XML path XMLNS path for delete
     */
    const XML_PATH_API_XMLNS_FOR_DELETE = 'msdynamic/general/xmlns_for_delete';

    /**
     * XML Path is enabled log
     */
    const XMP_PATH_IS_LOG_ENABLE = 'msdynamic/general/enable_log';

    /**
     * XML Path API function name insert new customer
     */
    const CREATE_CUSTOMER_IN_ERP = 'customercreate';

    /**
     * XML path Soap action create customer
     */
    const CREATE_CUSTOMER_SOAP_ACTION = 'Create';

    /**
     * XML Path API function name get customer list
     */
    const GET_CUSTOMER_LIST_IN_ERP = 'customer';

    /**
     * XML path SOAP action get customer list
     */
    const GET_CUSTOMER_LIST_SOAP_ACTION = 'ReadMultiple';

    /**
     * XML Path API function name get single customer
     */
    const GET_CUSTOMER_IN_ERP = 'GetCustomer';

    /**
     * XML Path API function name Ack customer
     */
    const ACK_CUSTOMER_IN_ERP = 'customerack';

    /**
     * XML path Ack customer SOAP action
     */
    const ACK_CUSTOMER_SOAP_ACTION = 'CreateMultiple';

    /**
     * XML Path API function name update customer
     */
    const UPDATE_CUSTOMER_IN_ERP = 'customerupdate';

    /**
     * XML path update customer SOAP action
     */
    const UPDATE_CUSTOMER_SOAP_ACTION = 'Create';

    /**
     * XML path API function name delete customer
     */
    const DELETE_CUSTOMER_IN_ERP = 'BlockCustomer';

    /**
     * XML path delete customer SOAP action
     */
    const DELETE_CUSTOMER_IN_ERP_SOAP_ACTION = 'BlockCustomer';

    /**
     * XML Path API function to get reward point from ERP
     */
    const GET_REWARD_POINT_FROM_ERP = 'customerpoint';

    /**
     * XML Path API soap action to get reward point from ERP
     */
    const GET_REWARD_POINT_SOAP_ACTION = 'Read';

    /**
     * XML Path API function to Earn reward point
     */
    const EARN_REWARD_POINT_FROM_MAGETNO = 'pointearn';

    /**
     * XML Path API soap action to Earn reward point
     */
    const EARN_REWARD_POINT_SOAP_ACTION = 'Create';

    /**
     * XML Path API function for Redeem reward point
     */
    const REDEEM_REWARD_POINT_FROM_MAGETNO = 'pointredeem';

    /**
     * XML Path API soap action for Redeem reward point
     */
    const REDEEM_REWARD_POINT_SOAP_ACTION = 'Create';

    /**
     * XML Path API function to get last updated point from Erp
     */
    const LAST_UPDATED_POINT_FROM_ERP = 'lastupdatedpoint';

    /**
     * XML Path API soap action to get last updated point from Erp
     */
    const LAST_UPDATED_POINT_SOAP_ACTION = 'ReadMultiple';

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

    /**
     * Array to identify the customer aleady exist in ERP message
     */
    const CUSTOMER_ALREADY_EXIST_MESSAGE_ARRAY = [
        0 => 'Customer',
        1 => 'already',
        2 => 'exist',
    ];

    /**
     * XML Path API function name get product list
     */
    const GET_PRODUCT_LIST_IN_ERP = 'product';

    /**
     * XML Path API function name get product list soap action
     */
    const GET_PRODUCT_LIST_SOAP_ACTION = 'ReadMultiple';

    /**
     * XML Path API function name Ack product
     */
    const ACK_PRODUCT_IN_ERP = 'productack';

    /**
     * XML Path API function name Ack product soap action
     */
    const ACK_PRODUCT_SOAP_ACTION = 'CreateMultiple';

    /**
     * XML Path API function name get product list
     */
    const GET_PRODUCT_INVENTORY_STOCK_ERP = 'productinventory';

    /**
     * XML Path API function name get product list soap action
     */
    const GET_PRODUCT_INVENTORY_STOCK_SOAP_ACTION = 'ProductInventory';

    /**
     * XML Path API function create orders
     */
    const CREATE_ORDER_IN_ERP = 'order';

    /**
     * XML path create order SOAP action
     */
    const CREATE_ORDER_SOAP_ACTION = 'Create';
}
