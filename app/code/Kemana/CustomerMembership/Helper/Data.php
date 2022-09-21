<?php
/*/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_CustomerMembership
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\CustomerMembership\Helper;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Kemana\Promotion\Api\PromotionRepositoryInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

/**
 * Class Data
 * @package Kemana\Promotion\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check module enable or not
     */
    const XML_PATH_CUSTOMER_MEMBERSHIP_IS_ENABLE = 'membership/general/is_enabled';

    /**
     * Get membership level admin configurations values
     */
    const XML_PATH_CUSTOMER_MEMBERSHIP_LEVELS = 'membership/general/membership_levels';

    /**
     * Enabled the log
     */
    const XML_PATH_IS_ENABLE_LOG = 'membership/general/enable_log';

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContextInterface;

    /**
     * @var \Kemana\SourceDistanceShipping\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $customerGroupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Authorization\Model\UserContextInterface $userContextInterface
     * @param \Kemana\SourceDistanceShipping\Logger\Logger $logger
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface        $storeManager,
        \Magento\Authorization\Model\UserContextInterface $userContextInterface,
        \Kemana\SourceDistanceShipping\Logger\Logger      $logger,
        \Magento\Customer\Api\GroupRepositoryInterface    $customerGroupRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder      $searchCriteriaBuilder,
        \Magento\Framework\App\Helper\Context             $context
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->userContextInterface = $userContextInterface;
        $this->logger = $logger;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @return mixed
     */
    public function isCustomerMembershipEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_MEMBERSHIP_IS_ENABLE, $this->storeScope);
    }

    /**
     * @return mixed
     */
    public function getCustomerMembershipLevels()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_MEMBERSHIP_LEVELS, $this->storeScope);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaFolderUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return int|null
     */
    public function getLoggedInCustomerId(): ?int
    {
        return $this->userContextInterface->getUserId();
    }

    /**
     * @return mixed
     */
    public function isEnableLog()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLE_LOG, $this->storeScope);
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

        $message = 'CustomerMemberShip : ' . $message;

        if ($type == 'info') {
            $this->logger->info($message);
        } elseif ($type == 'error') {
            $this->logger->error($message);
        } elseif ($type == 'notice') {
            $this->logger->notice($message);
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroups(): array
    {
        $searchCriteriaCustomerGroups = $this->searchCriteriaBuilder->create();

        $allGroups = $this->customerGroupRepository->getList($searchCriteriaCustomerGroups);

        if (count($allGroups->getItems())) {
            return $allGroups->getItems();
        }

        return [];
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroupsIdWithName()
    {
        $groups = [];

        foreach ($this->getCustomerGroups() as $group) {
            $groups[$group->getId()] = $group->getCode();
        }

        return $groups;
    }

}
