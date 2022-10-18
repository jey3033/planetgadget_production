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
 * @author   Jalpa Patel <jalpa@kemana.com>
 */

namespace Kemana\MsDynamics\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ReviewSubmit
 */
class ReviewSubmit implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $rewardData;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Reward
     */
    protected $erpReward;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->rewardData = $rewardData;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->erpReward = $erpReward;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Update points balance after review submit
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        if (!$this->helper->isEnable()) {
            return;
        }

        /* @var $review \Magento\Review\Model\Review */
        $review = $observer->getEvent()->getObject();
        $storeId = $review->getStoreId() ?: $this->storeManager->getStore()->getId();
        $websiteId = $storeId ? $this->storeManager->getStore($storeId)->getWebsiteId()
            : $this->storeManager->getStore()->getWebsiteId();
        if (!$this->rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        $point = $this->scopeConfig->getValue(
            'magento_reward/points/review',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        if($review->getCustomerId()) {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            if ($point && $customer->getCustomAttribute('ms_dynamic_customer_number')) {            
                $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
                if ($erpCustomerNumber) {
                    $this->erpReward->addCustomerEarnPointToErp($review->getCustomerId(), $erpCustomerNumber, $point);
                }
            }
        }
        return $this;
    }
}
