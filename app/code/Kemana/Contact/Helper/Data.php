<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Contact
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Contact\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * Get value for contact info is_enabled
     */
    const XML_PATH_ENABLE_CONTACT_INFO = 'kemana_acceleratorbase/kemana_contactinfo/is_enabled';

    /**
     * Get value for contact info is_enabled
     */
    const XML_PATH_BLOCK_STATIC_ID_CONTACT_INFO = 'kemana_acceleratorbase/kemana_contactinfo/block_static_id';

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
    public function isEnabledContactInfo()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_CONTACT_INFO, $this->storeScope);
    }

    /**
     * @return string|mixed
     */
    public function getBlockIdContactInfo()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BLOCK_STATIC_ID_CONTACT_INFO, $this->storeScope);
    }
}
