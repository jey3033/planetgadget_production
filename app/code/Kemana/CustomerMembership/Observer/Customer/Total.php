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

namespace Kemana\CustomerMembership\Observer\Customer;

/**
 * Class Total
 */
class Total implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var \Kemana\CustomerMembership\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\CustomerMembership\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        \Kemana\CustomerMembership\Helper\Data            $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->helper = $helper;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isCustomerMembershipEnabled()) {
            return;
        };

        $order = $observer->getEvent()->getOrder();

        // When creating invoice or Shipment
        if ($order->getStatus() == 'processing' || $order->getStatus() == 'complete') {

            // Check no remain to pay from order total
            if ($order->getBaseTotalDue() == 0) {

                // Check this order total not yet added to lifetime sale amount of the customer
                if (!$order->getIsCalculatedLifeTimeSaleAmount()) {

                    // Load the customer
                    if ($order->getCustomerId()) {

                        try {

                            $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());

                            if ($customer->getCustomAttribute('life_time_total_sale_amount')) {
                                $currentLifeTimeSale = (int)$customer->getCustomAttribute('life_time_total_sale_amount')->getValue();

                                $newLifeTimeSale = ((int)$order->getBaseGrandTotal() + $currentLifeTimeSale);

                                // Match new lifetime sale amount with membership levels
                                $goldMembershipAmount = $this->helper->getCustomerMembershipGoldAmount();
                                $platinumMembershipAmount = $this->helper->getCustomerMembershipPlatinumAmount();

                                $membershipGroupIds = $this->helper->getCustomerMembershipGroupIds();
                                $goldGroupId = $membershipGroupIds['gold'];
                                $platinumGroupId = $membershipGroupIds['platinum'];

                                if ($newLifeTimeSale < $platinumMembershipAmount) {
                                    $customer->setGroupId($goldGroupId);
                                }

                                if ($newLifeTimeSale > $goldMembershipAmount) {
                                    $customer->setGroupId($platinumGroupId);
                                }

                                // Update new lifetime sale amount in customer object
                                $customer->setCustomAttribute('life_time_total_sale_amount', $newLifeTimeSale);
                                $this->customerRepositoryInterface->save($customer);

                                // Update order
                                $order->setIsCalculatedLifeTimeSaleAmount(1);
                                $order->save();
                            }

                        } catch (\Exception $e) {
                            $this->helper->log("Error while processing the Customer ID ".$order->getCustomerId().". Order ID : ".$order->getId()." for
                            update the lifetime sale amount and the membership level. Error : ".$e->getMessage());
                        }

                    } else {
                        $this->helper->log("Order ID " . $order->getId() . " is not calculating lifetime sale amount because this
                        is guest order");
                    }

                } else {
                    $this->helper->log("Order ID " . $order->getId() . " is not calculate lifetime sales this time because this has already calculate
                    lifetime sale before this time.");
                }

            } else {
                $this->helper->log("Order ID " . $order->getId() . " is not processing for calculate lifetime sale because
                it is still not fully paid. Due amount to paid " . $order->getBaseTotalDue());
            }
        }
    }


}
