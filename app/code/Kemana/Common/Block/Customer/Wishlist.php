<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\Common\Block\Customer;

/**
 * Wishlist block customer items.
 *
 * @api
 * @since 100.0.2
 */
class Wishlist extends \Magento\Wishlist\Block\Customer\Wishlist
{
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Wish List'));
        $this->getChildBlock('wishlist_item_pager')
            ->setUseContainer(
                true
            )->setShowAmounts(
                true
            )->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                6
            )
            ->setCollection($this->getWishlistItems());
        return $this;
    }
}