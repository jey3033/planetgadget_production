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

namespace Kemana\MsDynamics\Model\ResourceModel\ApiTransportLog;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	public function _construct() {
		$this->_init("Kemana\MsDynamics\Model\ApiTransportLog","Kemana\MsDynamics\Model\ResourceModel\ApiTransportLog");
	}
}