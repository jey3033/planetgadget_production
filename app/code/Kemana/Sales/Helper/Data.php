<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Sales
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Sales\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Get value for tracking modal is_enabled
     */
    const XML_PATH_ENABLE_TRACKING_MODAL = 'kemana_acceleratorbase/tracking_modal/is_enabled';

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @param Context $context
     */
    public function __construct(
        Context               $context
    )
    {
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        parent::__construct($context);
    }

    /**
     * @return int
     */
    public function isEnabledTrackingModal()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_TRACKING_MODAL, $this->storeScope);
    }

}
