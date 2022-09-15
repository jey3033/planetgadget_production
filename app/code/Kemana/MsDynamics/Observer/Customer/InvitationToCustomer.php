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
 * Class InvitationToCustomer
 */
class InvitationToCustomer implements ObserverInterface
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
     * Update points balance after customer registered by invitation
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

        /* @var $invitation \Magento\Invitation\Model\Invitation */
        $invitation = $observer->getEvent()->getInvitation();
        $websiteId = $this->storeManager->getStore($invitation->getStoreId())->getWebsiteId();
        if (!$this->rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        $point = $this->scopeConfig->getValue(
            'magento_reward/points/invitation_customer',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );

        $customer = $this->customerRepository->getById($invitation->getCustomerId());
        if ($point && $customer->getCustomAttribute('ms_dynamic_customer_number')) {            
            $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
            if ($erpCustomerNumber) {
                $this->erpReward->addCustomerEarnPointToErp($invitation->getCustomerId(), $erpCustomerNumber, $point);
            }
        }
        return $this;
    }
}
