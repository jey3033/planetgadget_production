<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_GiftRegistry
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\GiftRegistry\Plugin\Block\Customer\Address;

/**
 * Class Edit
 */
class Edit
{
    
    /**
     * Return html select input element for Address (None/<address1>/<address2>/New/) change to --select address -- default empty
     *
     * @param \Magento\GiftRegistry\Block\Customer\Address\Edit $subject
     * @param mixed $result
     * @param string $domId
     * @return mixed
     */
    public function afterGetAddressHtmlSelect(\Magento\GiftRegistry\Block\Customer\Address\Edit $subject, $result, $domId = 'address_type_or_id')
    {
        if ($subject->isCustomerLoggedIn()) {
            $options = [
                [
                    'value' => \Magento\GiftRegistry\Helper\Data::ADDRESS_NONE,
                    'label' => $subject->escapeHtmlAttr(__('-- Select Address --'))
                ]
            ];
            foreach ($subject->getCustomer()->getAddresses() as $address) {
                $options[] = ['value' => $address->getId(), 'label' => $address->format('oneline')];
            }
            $options[] = [
                'value' => \Magento\GiftRegistry\Helper\Data::ADDRESS_NEW,
                'label' => $subject->escapeHtmlAttr(__('New Address')),
            ];

            $select = $subject->getLayout()->createBlock(
                \Magento\Framework\View\Element\Html\Select::class
            )->setName(
                'address_type_or_id'
            )->setId(
                $domId
            )->setClass(
                'address-select'
            )->setOptions(
                $options
            );

            return $select->getHtml();
        }
        return '';
    }
}
