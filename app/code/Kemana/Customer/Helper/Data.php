<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Customer
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Customer\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Url\Helper\Data as UrlHelper;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var UrlHelper
     */
    protected $_urlHelper;

    /**
     * @param Context $context
     * @param UrlHelper $_urlHelper
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        UrlHelper $urlHelper
    ) {
        $this->_urlHelper      = $urlHelper;

        parent::__construct($context);
    }

    /**
     * @param string $type
     * @param int $product_id
     * @return string
     */
    public function getWishlistRemindMeUrl($type, $product_id)
    {
        return $this->_getUrl(
            'productalert/add/' . $type,
            [
                'product_id' => $product_id,
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->_urlHelper->getEncodedUrl()
            ]
        );
    }

    /**
     * Check whether stock alert is allowed
     *
     * @return bool
     */
    public function isStockAlertAllowed()
    {
        return $this->scopeConfig->isSetFlag(
            \Magento\ProductAlert\Model\Observer::XML_PATH_STOCK_ALLOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
