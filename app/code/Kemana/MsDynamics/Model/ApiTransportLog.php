<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  Proprietary
 *
 * @author   Tushar Korat <tushar@kemana.com>
 */
namespace Kemana\MsDynamics\Model;

class ApiTransportLog extends \Magento\Framework\Model\AbstractModel
{
	public function _construct() {
		$this->_init("Kemana\MsDynamics\Model\ResourceModel\ApiTransportLog");
	}
}