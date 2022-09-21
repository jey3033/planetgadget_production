<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kredivo\Payment\Model\Config\Source\Api;

use \Magento\Framework\Option\ArrayInterface;

class Environment implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'sandbox',
                'label' => __('Sandbox'),
            ),
            array(
                'value' => 'production',
                'label' => __('Production'),
            ),
        );
    }
}
