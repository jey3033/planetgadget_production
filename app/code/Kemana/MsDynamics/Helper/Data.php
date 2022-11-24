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

namespace Kemana\MsDynamics\Helper;

use Kemana\MsDynamics\Model\Config\ConfigProvider;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Kemana\MsDynamics\Logger\Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kemana\MsDynamics\Logger\Logger $logger
     * @param \Kemana\MsDynamics\Logger\InventoryLogger $inventoryLogger
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context             $context,
        \Kemana\MsDynamics\Logger\Logger                  $logger,
        \Kemana\MsDynamics\Logger\InventoryLogger         $inventoryLogger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
    )
    {
        $this->logger = $logger;
        $this->inventoryLogger = $inventoryLogger;
        $this->customerRepository = $customerRepository;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;

        parent::__construct($context);
    }

    /**
     * @param $message
     * @param string $type
     * @param $code
     * @return void
     */
    public function log($message, string $type = 'error', $code = '0')
    {
        if (!$this->isEnableLog()) {
            return;
        }

        $message = 'MsDynamicErp : ' . $message;

        if ($code != '0') {
            $message = $message . ' ERP Response Code : ' . $code . ". Error Response Error : " . $this->getMsDynamicErpResponses()[$code];
        }

        if ($type == 'info') {
            $this->logger->info($message);
        } elseif ($type == 'error') {
            $this->logger->error($message);
        } elseif ($type == 'notice') {
            $this->logger->notice($message);
        }
    }

    /**
     * @param $message
     * @param string $type
     * @param $code
     * @return void
     */
    public function inventorylog($message, string $type = 'info')
    {
        if (!$this->isEnableLog()) {
            return;
        }

        $message = 'MsDynamicErp : ' . $message;

        if ($type == 'info') {
            $this->inventoryLogger->info($message);
        } elseif ($type == 'error') {
            $this->inventoryLogger->error($message);
        } elseif ($type == 'notice') {
            $this->inventoryLogger->notice($message);
        }
    }


    /**
     * @return mixed
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_IS_ENABLE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function isApiMode()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_MODE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function isLiveApi()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_MODE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_URL, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiUrlForDelete()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_URL_FOR_DELETE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiUsername()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_USERNAME, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiPassword()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XMP_PATH_API_PASSWORD, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiXmnls()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_XMLNS, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getApiXmnlsForDelete()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_API_XMLNS_FOR_DELETE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function isEnableLog()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XMP_PATH_IS_LOG_ENABLE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getFunctionCreateCustomer()
    {
        return ConfigProvider::CREATE_CUSTOMER_IN_ERP;
    }

    public function getSoapActionCreateCustomer()
    {
        return ConfigProvider::CREATE_CUSTOMER_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionCustomerList()
    {
        return ConfigProvider::GET_CUSTOMER_LIST_IN_ERP;
    }

    public function getSoapActionGetCustomerList()
    {
        return ConfigProvider::GET_CUSTOMER_LIST_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionGetCustomer()
    {
        return ConfigProvider::GET_CUSTOMER_IN_ERP;
    }

    /**
     * @return mixed
     */
    public function getFunctionAckCustomer()
    {
        return ConfigProvider::ACK_CUSTOMER_IN_ERP;
    }

    public function getSoapActionAckCustomer()
    {
        return ConfigProvider::ACK_CUSTOMER_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionUpdateCustomer()
    {
        return ConfigProvider::UPDATE_CUSTOMER_IN_ERP;
    }

    public function getSoapActionUpdateCustomer()
    {
        return ConfigProvider::UPDATE_CUSTOMER_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionDeleteCustomer()
    {
        return ConfigProvider::DELETE_CUSTOMER_IN_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionDeleteCustomer()
    {
        return ConfigProvider::DELETE_CUSTOMER_IN_ERP_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getMsDynamicErpResponses()
    {
        return ConfigProvider::ERP_RESPONSES;
    }

    /**
     * @return mixed
     */
    public function getRewardPointFunction()
    {
        return ConfigProvider::GET_REWARD_POINT_FROM_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionGetRewardPoint()
    {
        return ConfigProvider::GET_REWARD_POINT_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function earnRewardPointFunction()
    {
        return ConfigProvider::EARN_REWARD_POINT_FROM_MAGETNO;
    }

    /**
     * @return mixed
     */
    public function getSoapActionEarnRewardPoint()
    {
        return ConfigProvider::EARN_REWARD_POINT_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function redeemRewardPointFunction()
    {
        return ConfigProvider::REDEEM_REWARD_POINT_FROM_MAGETNO;
    }

    /**
     * @return mixed
     */
    public function getSoapActionRedeemRewardPoint()
    {
        return ConfigProvider::REDEEM_REWARD_POINT_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getLastUpdatedPointFunction()
    {
        return ConfigProvider::LAST_UPDATED_POINT_FROM_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionlastUpdated()
    {
        return ConfigProvider::LAST_UPDATED_POINT_SOAP_ACTION;
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErp($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <' . $apiFunction . '>
                                ' . $postParameters . '
                            </' . $apiFunction . '>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToGetUnSyncCustomersFromApi($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <filter>
                                ' . $postParameters . '
                            </filter>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToGetUnSyncProductsFromApi($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <filter>
                                ' . $postParameters . '
                            </filter>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpAckListOfCustomers($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <' . $apiFunction . '_List>
                                ' . $postParameters . '
                            </' . $apiFunction . '_List>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpAckListOfProducts($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <' . $apiFunction . '_List>
                                ' . $postParameters . '
                            </' . $apiFunction . '_List>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpGetRewardPoint($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                    <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <CustomerNo>' . $postParameters . '</CustomerNo>
                        </' . $soapAction . '>
                    </Body>
                </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpEarnRewardPoint($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <' . $apiFunction . '>
                                ' . $postParameters . '
                            </' . $apiFunction . '>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpRedeemRewardPoint($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                            <' . $apiFunction . '>
                                ' . $postParameters . '
                            </' . $apiFunction . '>
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToErpGetLastUpdatedPoint($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnls() . "/" . $apiFunction . '">
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $pureArray
     * @return string
     */
    public function convertArrayToXml($pureArray): string
    {
        $xmlOutput = '';
        foreach ($pureArray as $nodeName => $nodeValue) {
            if (!$nodeValue) {
                $xmlOutput .= '<' . $nodeName . '/>';
                continue;
            }

            $xmlOutput .= '<' . $nodeName . '>' . $nodeValue . '</' . $nodeName . '>';
        }

        return $xmlOutput;
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToGetUnSyncInventorysFromApi($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                        <' . $soapAction . ' xmlns="' . $this->getApiXmnlsForDelete() . '">
                                ' . $postParameters . '
                        </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $ackCustomerData
     * @return string
     */
    public function convertAckCustomerListToXml($ackCustomerData): string
    {
        $xmlOutput = '';

        if (empty($ackCustomerData)) {
            return $xmlOutput;
        }

        foreach ($ackCustomerData as $ackCustomer) {
            $xmlOutput .= '<customerack>';
            foreach ($ackCustomer as $nodeName => $nodeValue) {
                $xmlOutput .= '<' . $nodeName . '>' . $nodeValue . '</' . $nodeName . '>';
            }
            $xmlOutput .= '</customerack>';

        }

        return $xmlOutput;
    }

    /**
     * @param $ackCustomerData
     * @return string
     */
    public function convertAckCustomerSingleToXml($ackCustomerData): string
    {
        $xmlOutput = '';

        if (empty($ackCustomerData)) {
            return $xmlOutput;
        }
        $xmlOutput .= '<customerack>';
        foreach ($ackCustomerData as $nodeName => $nodeValue) {
            $xmlOutput .= '<' . $nodeName . '>' . $nodeValue . '</' . $nodeName . '>';
        }
        $xmlOutput .= '</customerack>';

        return $xmlOutput;
    }

    /**
     * @param $apiFunction
     * @param $postParameters
     * @return string
     */
    public function getXmlRequestBodyToDeleteCustomer($apiFunction, $soapAction, $postParameters): string
    {
        return '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                <Body>
                       <' . $soapAction . ' xmlns="' . $this->getApiXmnlsForDelete() . '">
                           ' . $postParameters . '
                       </' . $soapAction . '>
                </Body>
        </Envelope>';
    }

    /**
     * @param $errorMessage
     * @return bool
     */
    public function checkAlreadyExistCustomerError($errorMessage): bool
    {
        $arrayMessage = explode(" ", $errorMessage);

        if (isset($arrayMessage[1])) {
            unset($arrayMessage[1]);
        }

        if (!count(array_diff($arrayMessage, $this->getCustomerAlreadyExistErrorInErp()))) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getCustomerAlreadyExistErrorInErp(): array
    {
        return ConfigProvider::CUSTOMER_ALREADY_EXIST_MESSAGE_ARRAY;
    }

    /**
     * @param $customerId
     * @param $customerNumber
     * @return bool
     */
    public function updateCustomerMsDynamicNumber($customerId, $customerNumber): bool
    {
        try {
            $getCustomer = $this->customerRepository->getById($customerId);
            $getCustomer->setCustomAttribute('ms_dynamic_customer_number', $customerNumber);
            $this->customerRepository->save($getCustomer);

            $this->log('Successfully updated the MsDynamicCustomerNumber in Magento for customer ' . $customerId, 'info');

            return true;
        } catch (\Exception $e) {
            $this->log('Failed to update Customer Number in Magento for Customer ' . $customerId . ' sent/update to ERP. Error :' . $e->getMessage(), 'info');
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getFunctionProductList()
    {
        return ConfigProvider::GET_PRODUCT_LIST_IN_ERP;
    }

    public function getSoapActionGetProductList()
    {
        return ConfigProvider::GET_PRODUCT_LIST_SOAP_ACTION;
    }

    /**
     * @param $ackProductData
     * @return string
     */
    public function convertAckProductListToXml($ackProductData): string
    {
        $xmlOutput = '';

        if (empty($ackProductData)) {
            return $xmlOutput;
        }

        foreach ($ackProductData as $ackProduct) {
            $xmlOutput .= '<productack>';
            foreach ($ackProduct as $nodeName => $nodeValue) {
                $xmlOutput .= '<' . $nodeName . '>' . $nodeValue . '</' . $nodeName . '>';
            }
            $xmlOutput .= '</productack>';

        }

        return $xmlOutput;
    }

    /**
     * @return mixed
     */
    public function getFunctionAckProduct()
    {
        return ConfigProvider::ACK_PRODUCT_IN_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionAckProduct()
    {
        return ConfigProvider::ACK_PRODUCT_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionInventoryStock()
    {
        return ConfigProvider::GET_PRODUCT_INVENTORY_STOCK_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionGetInventoryStock()
    {
        return ConfigProvider::GET_PRODUCT_INVENTORY_STOCK_SOAP_ACTION;
    }

    /**
     * @return mixed
     */
    public function getFunctionCreateOrder()
    {
        return ConfigProvider::CREATE_ORDER_IN_ERP;
    }

    /**
     * @return mixed
     */
    public function getSoapActionCreateOrder()
    {
        return ConfigProvider::CREATE_ORDER_SOAP_ACTION;
    }

    /**
     * @param $sourceLocationName
     * @return false|int|string|null
     */
    public function getSourceLocationCodeByName($sourceLocationName) {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('name', $sourceLocationName)
            ->create();

        $sourceData = $this->sourceRepository->getList($searchCriteria);

        if (count($sourceData->getItems())) {
            return array_key_first($sourceData->getItems());
        }

        return false;
    }
}
