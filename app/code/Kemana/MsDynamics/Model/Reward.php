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
 * @author   Jalpa Patel <jalpa@kemana.com>
 */

namespace Kemana\MsDynamics\Model;

/**
 * Class Reward
 */
class Reward extends \Magento\Reward\Model\Reward
{
    const REWARD_ACTION_FOR_ERP = 13;

    const REWARD_ACTION_FOR_NOT_MATCH_ERP_POINT = 14;

    /**
     * Internal constructor
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Magento\Reward\Model\ResourceModel\Reward::class);
        self::$_actionModelClasses = self::$_actionModelClasses + [
                self::REWARD_ACTION_ADMIN => \Magento\Reward\Model\Action\Admin::class,
                self::REWARD_ACTION_ORDER => \Magento\Reward\Model\Action\Order::class,
                self::REWARD_ACTION_REGISTER => \Magento\Reward\Model\Action\Register::class,
                self::REWARD_ACTION_NEWSLETTER => \Magento\Reward\Model\Action\Newsletter::class,
                self::REWARD_ACTION_INVITATION_CUSTOMER => \Magento\Reward\Model\Action\InvitationCustomer::class,
                self::REWARD_ACTION_INVITATION_ORDER => \Magento\Reward\Model\Action\InvitationOrder::class,
                self::REWARD_ACTION_REVIEW => \Magento\Reward\Model\Action\Review::class,
                self::REWARD_ACTION_ORDER_EXTRA => \Magento\Reward\Model\Action\OrderExtra::class,
                self::REWARD_ACTION_CREDITMEMO => \Magento\Reward\Model\Action\Creditmemo::class,
                self::REWARD_ACTION_SALESRULE => \Magento\Reward\Model\Action\Salesrule::class,
                self::REWARD_ACTION_REVERT => \Magento\Reward\Model\Action\OrderRevert::class,
                self::REWARD_ACTION_CREDITMEMO_VOID => \Magento\Reward\Model\Action\Creditmemo\VoidAction::class,
                self::REWARD_ACTION_FOR_ERP => \Kemana\MsDynamics\Model\Action\Erp::class,
                self::REWARD_ACTION_FOR_NOT_MATCH_ERP_POINT => \Kemana\MsDynamics\Model\Action\NotMatchErp::class,
            ];
    }
}
