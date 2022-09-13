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
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $rewardFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $_responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
        $this->customer = $customer;
        $this->currentCustomer = $currentCustomer;
        $this->rewardFactory = $rewardFactory;
        $this->storeManager = $storeManager;
        $this->redirect = $redirect;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
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
        $customer = $this->customer->load($customerId);
        $erpCustomerNumber = $customer->getMsDynamicCustomerNumber();
        $storeId = $this->storeManager->getStore()->getId();

        $reward = $this->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
        $magentoRewardPointBalance =  $reward->getPointsBalance();

        if ($customer->getMsDynamicCustomerNumber()) {
            $dataToGetRewardPoint = [
                "customer_no" => $erpCustomerNumber
            ];

            $dataToGetRewardPoint = $this->helper->convertArrayToXml($dataToGetRewardPoint);
            $getRewardPoint = $this->erpCustomer->getRewardPointFromErp($this->helper->getRewardPointFunction(),
                $this->helper->getSoapActionGetRewardPoint(), $dataToGetRewardPoint);

            if (isset($getRewardPoint['curlStatus']) == '200') {

                if($getRewardPoint['response']['PointBalance'] !== $magentoRewardPointBalance) {
                    $this->getRewardModel()
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

    /**
     * Get reward model
     *
     * @return \Magento\Reward\Model\Reward
     * @codeCoverageIgnore
     */
    protected function getRewardModel()
    {
        return $this->rewardFactory->create();
    }
}
