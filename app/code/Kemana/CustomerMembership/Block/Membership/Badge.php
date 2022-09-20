<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_CustomerMembership
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\CustomerMembership\Block\Membership;

use Magento\Framework\View\Element\Template;

/**
 * Class Badge
 */
class Badge extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Kemana\CustomerMembership\Model\Customer\Badge
     */
    protected $badgeModel;

    /**
     * @param \Kemana\CustomerMembership\Model\Customer\Badge $badgeModel
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Kemana\CustomerMembership\Model\Customer\Badge $badgeModel,
        Template\Context                                $context,
        array                                           $data = [])
    {
        $this->badgeModel = $badgeModel;
        parent::__construct($context, $data);
    }

    /**
     * @return false|string
     */
    public function getCustomerMembershipBadge()
    {
        return $this->badgeModel->getCustomerMembershipBadge();
    }

}
