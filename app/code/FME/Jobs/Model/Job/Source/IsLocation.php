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
namespace FME\Jobs\Model\Job\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsLocation implements OptionSourceInterface
{
    protected $departments;

    public function __construct(\FME\Jobs\Model\Job $departments)
    {
        $this->departments = $departments;
    }
    
    public function toOptionArray()
    {   
        $availableOptions = $this->departments->getLocations();         
        $options []= ['label' => '--Location--',
                'value' => ''];
        foreach ($availableOptions as $deps) {
            $options[] = [
                'label' => $deps['data_name'],
                'value' => $deps['data_code'],
            ];
        }
        return $options;
    }
}
