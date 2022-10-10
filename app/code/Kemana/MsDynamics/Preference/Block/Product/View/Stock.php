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
class Stock extends \Magento\ProductAlert\Block\Product\View
{

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data $msHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\ProductAlert\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Helper\PostHelper $coreHelper,
        array $data = []
    )
    {
        parent::__construct($context, $helper, $registry, $coreHelper, $data);
        $this->helper = $msHelper;
    }

    /**
     * Prepare stock info
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        if (!$this->_helper->isStockAlertAllowed() || !$this->getProduct()) {
            $template = '';
        } else {
            $this->setSignupUrl($this->_helper->getSaveUrl('stock'));
        }
        if($this->getProduct()->isAvailable() && !$this->helper->isEnable()){
            $template = '';
        }
        return parent::setTemplate($template);
    }
}