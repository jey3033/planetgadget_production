<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SourceDistanceShipping
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SourceDistanceShipping\Helper;

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
     * @var \Kemana\SourceDistanceShipping\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var \Magento\Customer\Model\Address\Mapper
     */
    protected $addressMapper;

    /**
     * Google API Key - Under Shipping Method
     */
    const XML_PATH_GOOGLE_API_KEY = 'carriers/source_distance_based_shipping/google_api_key';

    /**
     * Enabled the log
     */
    const XML_PATH_ENABLE_LOG = 'carriers/source_distance_based_shipping/enable_log';

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Kemana\SourceDistanceShipping\Logger\Logger $logger
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context        $context,
        \Kemana\SourceDistanceShipping\Logger\Logger $logger,
        \Magento\Customer\Model\Address\Config       $addressConfig,
        \Magento\Customer\Model\Address\Mapper       $addressMapper,
        \Magento\Store\Model\StoreManagerInterface   $storeManager
    )
    {
        $this->logger = $logger;
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
        $this->storeManager = $storeManager;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        parent::__construct($context);
    }

    /**
     * @param $message
     * @param string $type
     * @return void
     */
    public function log($message, string $type = 'error')
    {
        if (!$this->isEnableLog()) {
            return;
        }

        $message = 'SourceDistanceBaseShipping : ' . $message;

        if ($type == 'info') {
            $this->logger->info($message);
        } elseif ($type == 'error') {
            $this->logger->error($message);
        } elseif ($type == 'notice') {
            $this->logger->notice($message);
        }
    }

    /**
     * Prepare the address sting by given object
     *
     * @param $addressObject
     * @return string|null
     */
    public function prepareAddressString($addressObject): ?string
    {
        $addressString = null;

        if ($addressObject->getData('street')) {
            $addressString .= $addressObject->getData('street');
        }

        if ($addressObject->getData('district')) {
            $addressString .= ' ' . $addressObject->getData('district');
        }

        if ($addressObject->getData('city')) {
            $addressString .= ' ' . $addressObject->getData('city');
        }

        if ($addressObject->getData('region')) {
            $addressString .= ' ' . $addressObject->getData('region');
        }

        if ($addressObject->getData('postcode')) {
            $addressString .= ' ' . $addressObject->getData('postcode');
        }

        return $addressString;
    }

    /**
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_API_KEY, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function isEnableLog()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_LOG, $this->storeScope);
    }

}
