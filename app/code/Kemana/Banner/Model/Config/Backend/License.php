<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Banner
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anton Vinoj <avinoj@kemana.com>
 */

namespace Kemana\Banner\Model\Config\Backend;

use Magento\Store\Model\ScopeInterface;
use Kemana\Banner\Helper\Data as BannerHelper;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Config\Value;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Class License
 * @package Kemana\Banner\Model\Config\Backend
 */
class License extends Value
{
    /**
     * @var BannerHelper 
     */
    protected $bannerHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * License constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param BannerHelper $bannerHelper
     * @param StoreManagerInterface $storeManager
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        BannerHelper $bannerHelper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->bannerHelper = $bannerHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return bool|Value
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSave()
    {
        $value = (string)$this->getValue();
        $baseLinkUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_DIRECT_LINK);
        $isValid = $this->bannerHelper->isValid($baseLinkUrl, $value , $this->bannerHelper->getModule());
        if ($isValid) {
            $this->_dataSaveAllowed = true;
            return true;
        } else {
            $this->_dataSaveAllowed = false;
            return false;
        }
    }
}
