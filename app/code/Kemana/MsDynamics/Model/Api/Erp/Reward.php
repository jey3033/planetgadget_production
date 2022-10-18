<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Jalpa Patel <jalpa@kemana.com>
 */

namespace Kemana\MsDynamics\Model\Api\Erp;

/**
 * Class Reward
 */
class Reward
{
    /**
     * @var \Kemana\MsDynamics\Model\Api\Request
     */
    protected $request;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $rewardFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @param \Kemana\MsDynamics\Model\Api\Request $request
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Kemana\MsDynamics\Model\Api\Request $request,
        \Kemana\MsDynamics\Helper\Data $helper,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->request = $request;
        $this->helper = $helper;
        $this->rewardFactory = $rewardFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $customerData
     * @return array
     */
    public function getRewardPointFromErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpGetRewardPoint($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return [];
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $customerData
     * @return array
     */
    public function earnRewardPointToErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpEarnRewardPoint($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return [];
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $customerData
     * @return array
     */
    public function redeemRewardPointToErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpRedeemRewardPoint($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return [];
    }

    /**
     * @param $apiFunction
     * @param $soapAction
     * @param $customerData
     * @return array
     */
    public function LastUpdatedPointFromErp($apiFunction, $soapAction, $customerData)
    {
        $postParameters = $customerData;

        $getCustomerFromErp = $this->request->apiTransport($apiFunction, $soapAction,
            $this->helper->getXmlRequestBodyToErpGetLastUpdatedPoint($apiFunction, $soapAction, $postParameters));

        if (isset($getCustomerFromErp['response'])) {
            return $getCustomerFromErp;
        }

        return [];
    }

    /**
     * @param $customerId
     * @param $customerNumber
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $customer
     * @param $pointsDelta
     * @return bool
     */
    public function addCustomerEarnPointToErp($customerId, $customerNumber, $pointsDelta = 0): bool
    {
        $this->helper->log('REWARD POINT : Start Earn point Event - ' . $customerId, 'info');
        try {
            $reward = $this->getRewardModel()->getCollection()->addFieldToFilter('customer_id', ['eq' => $customerId])->getFirstItem();
            if($pointsDelta > 0) {
                $magentoRewardPointBalance = $pointsDelta;
            } else {
                $magentoRewardPointBalance = $reward->getPointsBalance();
            }

            $dataToErp = [
                "DocumentNo" => $this->getTimeStamp().'-'.$customerId,
                "CustomerNo" => $customerNumber,
                "Description" => 'Earn point',
                "Points" => $magentoRewardPointBalance
            ];

            $dataToErp = $this->helper->convertArrayToXml($dataToErp);

            $earnPointToErp = $this->earnRewardPointToErp($this->helper->earnRewardPointFunction(),
            $this->helper->getSoapActionEarnRewardPoint(), $dataToErp);

            if (empty($earnPointToErp)) {
                $this->helper->log('REWARD POINT : ERP system might be off line', 'error');
                return false;
            }

            if ($earnPointToErp['curlStatus'] == 200 && isset($earnPointToErp['response']['CustomerNo'])) {
                $this->helper->log('REWARD POINT : Successfully added Earn Point To ERP for customer ' . $customerId, 'info');
            }

            return true;
        } catch (\Exception $e) {
            $this->helper->log('REWARD POINT : Failed to sent Earn point to Erp for Customer ' . $customerId . ' Error :' . $e->getMessage(), 'info');
        }

        return false;
    }

    /**
     * Get reward model
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function getRewardModel()
    {
        return $this->rewardFactory->create();
    }

    /**
     * Get Current date Timestamp
     *
     * @return string
     */
    public function getTimeStamp()
    {
        $dateToTimestamp = $this->dateTime->gmtDate();
        $timeStamp = $this->dateTime->gmtTimestamp($dateToTimestamp);
        return $timeStamp;
    }
}
