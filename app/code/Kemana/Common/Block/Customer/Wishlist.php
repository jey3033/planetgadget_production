<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @author     Magento Core Team <core@magentocommerce.com>
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
        parent::_prepareLayout();
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