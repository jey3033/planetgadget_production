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

class InvitationToCustomer implements ObserverInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->_rewardData = $rewardData;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->scopeConfig = $scopeConfig;
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Update points balance after first successful subscribtion
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
        $websiteId = $this->_storeManager->getStore($invitation->getStoreId())->getWebsiteId();
        if (!$this->_rewardData->isEnabledOnFront($websiteId)) {
            return $this;
        }

        $point = $this->scopeConfig->getValue(
            'magento_reward/points/invitation_customer',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        if ($point) {
            $customerModel = $this->customerRegistry->retrieve($invitation->getCustomerId());
            if ($customerModel->getMsDynamicCustomerNumber()) {
                $customerNumber = $customerModel->getMsDynamicCustomerNumber();
                $this->helper->addCustomerEarnPointToErp($invitation->getCustomerId(), $customerNumber, $this->erpCustomer, $point);
            }
        }
        return $this;
    }
}
