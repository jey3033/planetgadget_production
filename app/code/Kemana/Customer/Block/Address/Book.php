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

namespace Kemana\Customer\Block\Address;

/**
 * Customer address book block
 */
class Book extends \Magento\Customer\Block\Address\Book
{

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressMapPoint(\Magento\Customer\Api\Data\AddressInterface $address = null)
    {
        try {
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
