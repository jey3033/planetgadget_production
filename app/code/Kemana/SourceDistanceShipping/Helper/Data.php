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

use Magento\Framework\App\Helper\Context;
use Kemana\SourceDistanceShipping\Logger\Logger;
use Magento\Customer\Model\Address\Config as AddressConfig;
use Magento\Customer\Model\Address\Mapper as AddressMappaer;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Context
     */
    protected $context;

    protected $logger;

    protected $addressConfig;

    protected $addressMapper;


    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Logger $logger,
        AddressConfig $addressConfig,
        AddressMappaer $addressMapper
    ) {
        $this->logger = $logger;
        $this->addressConfig = $addressConfig;
        $this->addressMapper = $addressMapper;
        parent::__construct($context);
    }

    public function log($message, $type = 'info') {

        $message = 'SourceDistanceBaseShipping : '.$message;

        if ($type == 'info') {
            $this->logger->info($message);
        } elseif ($type == 'error') {
            $this->logger->error($message);
        } elseif ($type == 'notice') {
            $this->logger->notice($message);
        }
    }

    public function getFullAddressString($address) {
        $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();

        return $renderer->renderArray($this->addressMapper->toFlatArray($address));

    }

    public function prepareAddressString($addressObject) {
        $addressString = null;

        if ($addressObject->getData('street')) {
            $addressString .= $addressObject->getData('street');
        }

        if ($addressObject->getData('district')) {
            $addressString .= ' '.$addressObject->getData('district');
        }

        if ($addressObject->getData('city')) {
            $addressString .= ' '.$addressObject->getData('city');
        }

        if ($addressObject->getData('region')) {
            $addressString .= ' '.$addressObject->getData('region');
        }

        if ($addressObject->getData('postcode')) {
            $addressString .= ' '.$addressObject->getData('postcode');
        }

        return $addressString;
    }

}
