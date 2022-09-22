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

namespace Kemana\MsDynamics\Model\Action;

/**
 * Class NotMatchErp
 */
class NotMatchErp extends \Magento\Reward\Model\Action\AbstractAction
{
    /**
     * Check whether rewards can be added for action
     *
     * @return bool
     */
    public function canAddRewardPoints()
    {
        return true;
    }

    /**
     * Return action message for history log
     *
     * @param array $args Additional history data
     * @return \Magento\Framework\Phrase
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getHistoryMessage($args = [])
    {
        return __('Updated Points as not matched with Erp');
    }
}
