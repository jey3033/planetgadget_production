<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Model\Config\Source;

/**
 * Class Status
 * @package Kemana\Promotion\Model\Config\Source
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Active status lable
     */
    const STATUS_ACTIVE_LABEL = 'Active';

    /**
     * Inactive status lable
     */
    const STATUS_INACTIVE_LABEL = 'Inactive';

    /**
     * Active status value in promotion table
     */
    const STATUS_ACTIVE_VAL = 1;

    /**
     * Inactive status value in promotion table
     */
    const STATUS_INACTIVE_VAL = 0;

    /**
     * Returns an option array representation of the object.
     *
     * @return     array  Option array representation of the object.
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_INACTIVE_VAL, 'label' => __(self::STATUS_INACTIVE_LABEL)],
            ['value' => self::STATUS_ACTIVE_VAL, 'label' => __(self::STATUS_ACTIVE_LABEL)],
        ];
    }

    /**
     * return status data in array
     *
     * @param bool $defaultValues The default values
     *
     * @return     array
     */
    public function toArray($defaultValues = false)
    {
        $options = array();

        if ($defaultValues) {
            $options[''] = '';
        }

        $options[self::STATUS_INACTIVE_VAL] = __(self::STATUS_INACTIVE_LABEL);
        $options[self::STATUS_ACTIVE_VAL] = __(self::STATUS_ACTIVE_LABEL);

        return $options;
    }
}
