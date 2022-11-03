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

namespace Kemana\Common\Block\Product\View;

/**
 * Detailed Product Reviews
 *
 * @api
 * @since 100.0.2
 */
class ListView extends \Magento\Review\Block\Product\View\ListView
{
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');

        if ($toolbar) {
            $toolbar->setLimit(
                3
            )->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }
}