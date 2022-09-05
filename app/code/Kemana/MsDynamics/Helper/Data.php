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

    protected $customerRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kemana\MsDynamics\Logger\Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context             $context,
        \Kemana\MsDynamics\Logger\Logger                  $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

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
     * @return mixed
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(ConfigProvider::XML_PATH_IS_ENABLE, $this->storeScope);
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
            $this->log('End Customer Register Success Event - Failed to update Customer Number in Magento for Customer ' . $customerId . ' sent/update to ERP. Error :' . $e->getMessage(), 'info');
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
}
