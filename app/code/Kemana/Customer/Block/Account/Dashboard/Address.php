<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Customer
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Customer\Block\Account\Dashboard;

/**
 * Class to manage customer dashboard addresses section
 */
class Address extends \Magento\Customer\Block\Account\Dashboard\Address
{

    /**
     * HTML for Shipping Address Map Point
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getPrimaryShippingAddressMapPoint()
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultShippingAddress();
            if ($address) {
                if ($map_point = $address->getCustomAttribute('map_point')) {
                    if (!empty($map_point->getValue())) {
                        return "Already Pinpoint";
                    }
                } else {
                    return "Not Pinpoint yet";
                }
            }
        } catch (NoSuchEntityException $e) {
            $address = '';
        }
    }

    /**
     * HTML for Billing Address Map Point
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getPrimaryBillingAddressMapPoint()
    {
        try {
            $address = $this->currentCustomerAddress->getDefaultBillingAddress();
            if ($address) {
                if ($map_point = $address->getCustomAttribute('map_point')) {
                    if (!empty($map_point->getValue())) {
                        return "Already Pinpoint";
                    }
                } else {
                    return "Not Pinpoint yet";
                }
            }
        } catch (NoSuchEntityException $e) {
            $address = '';
        }
        return '';
    }
}
