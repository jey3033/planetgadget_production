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
namespace FME\Jobs\Model;

class Mdata extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    protected $_logger;

    protected function _construct()
    {
        $this->_init('FME\Jobs\Model\ResourceModel\Mdata');
    }
    
    public function getAvailableStatuses()
    {
        $availableOptions = ['0' => 'Disable',
                           '1' => 'Enable'];
        return $availableOptions;
    }    
}
