<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Kemana\MsDynamics\Preference\Block\Product\View;

/**
 * Recurring payment view stock
 *
 * @api
 * @since 100.0.2
 */
class Stock extends \Magento\ProductAlert\Block\Product\View\Stock
{
    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        
        $this->setSignupUrl($this->_helper->getSaveUrl('stock'));
        return parent::setTemplate($template);
    }
}
