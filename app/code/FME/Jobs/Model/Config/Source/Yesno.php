<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_Jobs
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Jobs\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Yesno implements ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Yes',
                'value' => '1'
            ],
            1 => [
                'label' => 'No',
                'value' => '0'
            ],
        ];
        return $options;
    }
}
