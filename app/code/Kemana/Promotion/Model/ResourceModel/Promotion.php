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

namespace Kemana\Promotion\Model\ResourceModel;

/**
 * Class Promotion
 *
 * @package Kemana\Promotion\Model\ResourceModel
 */
class Promotion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Promotion constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Main table and primary key
     */
    protected function _construct()
    {
        $this->_init('kemana_promotion', 'promotion_id');
    }

}
