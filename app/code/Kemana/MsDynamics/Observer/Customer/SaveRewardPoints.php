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

declare(strict_types=1);

namespace Kemana\MsDynamics\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveRewardPoints
 */
class SaveRewardPoints implements ObserverInterface
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Reward
     */
    protected $erpReward;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Reward $erpReward
    ) {
        $this->helper = $helper;
        $this->erpReward = $erpReward;
    }

    /**
     * Update reward points for ERP customer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        if (!$this->helper->isEnable()) {
            return;
        }

        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost('reward');
        if ($data && !empty($data['points_delta']) && $customer->getCustomAttribute('ms_dynamic_customer_number')) {
            $this->validatePointsDelta((string)$data['points_delta']);
            
            $erpCustomerNumber = $customer->getCustomAttribute('ms_dynamic_customer_number')->getValue();
            if ($erpCustomerNumber) {
                $this->erpReward->addCustomerEarnPointToErp($customer->getId(), $erpCustomerNumber, $data['points_delta']);
            }
        }

        return $this;
    }

    /**
     * Validates reward points delta value.
     *
     * @param string $pointsDelta
     * @return void
     * @throws LocalizedException
     */
    private function validatePointsDelta(string $pointsDelta): void
    {
        if (filter_var($pointsDelta, FILTER_VALIDATE_INT) === false) {
            throw new LocalizedException(__('Reward points should be a valid integer number.'));
        }
    }
}
