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

/**
 * Class CustomerRewardPointInfo
 */
class CustomerRewardPointInfo implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->helper->log('-- Get Reward Point From ERP Start --.', 'info');

        $customerId = $this->currentCustomer->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
        $storeId = $this->storeManager->getStore()->getId();

        $reward = $this->helper->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
        $magentoRewardPointBalance =  $reward->getPointsBalance();

        if ($erpCustomerNumber) {
            $dataToGetRewardPoint = [
                "customer_no" => $erpCustomerNumber
            ];

            $dataToGetRewardPoint = $this->helper->convertArrayToXml($dataToGetRewardPoint);
            $getRewardPoint = $this->erpCustomer->getRewardPointFromErp($this->helper->getRewardPointFunction(),
                $this->helper->getSoapActionGetRewardPoint(), $dataToGetRewardPoint);

            if (isset($getRewardPoint['curlStatus']) == '200') {

                if($getRewardPoint['response']['PointBalance'] !== $magentoRewardPointBalance) {
                    $this->helper->getRewardModel()
                        ->setCustomerId($customerId)
                        ->setWebsiteId($this->storeManager->getStore($storeId)->getWebsiteId())
                        ->setPointsDelta($getRewardPoint['response']['PointBalance'])
                        ->setAction(\Kemana\MsDynamics\Model\Reward::REWARD_ACTION_FOR_ERP)
                        ->setActionEntity($customer)
                        ->updateRewardPoints();
                }

                $this->helper->log('Customer ERP Number ' . $erpCustomerNumber . ' Updated points from ERP. Magento ID ' . $customerId, 'info');
                $this->helper->log('-- End Get Reward Point From ERP --', 'info');
            }
        }
    }
}
