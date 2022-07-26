<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_CustomerMembership
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\CustomerMembership\Model\Customer;

/**
 * Class Badge
 */
class Badge extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    protected $userContext;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \Kemana\CustomerMembership\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Kemana\CustomerMembership\Helper\Data $helper
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Kemana\CustomerMembership\Helper\Data            $helper
    )
    {
        $this->userContext = $userContext;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->helper = $helper;
    }

    /**
     * @param $customerID
     * @return false|string
     */
    public function getCustomerMembershipBadge($customerID = null)
    {
        if (!$this->helper->isCustomerMembershipEnabled()) {
            return false;
        };

        if (!$customerID) {
            $customerID = $this->userContext->getUserId();
        }

        if (!$customerID) {
            return false;
        }

        try {

            $customer = $this->customerRepositoryInterface->getById($customerID);

            $membershipGroupIds = $this->helper->getCustomerMembershipGroupIds();
            $goldGroupId = $membershipGroupIds['gold'];
            $platinumGroupId = $membershipGroupIds['platinum'];

            if ($customer->getGroupId() == $goldGroupId) {
                return $this->helper->getMembershipGoldCode();
            }

            if ($customer->getGroupId() == $platinumGroupId) {
                return $this->helper->getMembershipPlatinumCode();
            }

        } catch (\Exception $e) {
            $this->helper->log('Error while processing customer ID : ' . $customerID . ' to show the badge in customer
            dashboard. Error : ' . $e->getMessage());
        }

        return false;
    }
}
