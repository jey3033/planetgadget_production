<?php /** @noinspection PhpUndefinedMethodInspection */

/**
 * Copyright © 2017 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */
/**
 * @category Kemana
 * @package  Kemana_ShippingInsurance
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anupam Tiwari<anupam.tiwari@kemana.com>
 */

namespace Kemana\ShippingInsurance\Block\Adminhtml\Sales;

use Kemana\ShippingInsurance\Helper\Data;
use Magento\Directory\Model\Currency;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\DataObject;

/**
 * Class Totals
 * @package Kemana\ShippingInsurance\Block\Adminhtml\Sales
 */
class Totals extends Template
{

    /**
     * @var Data
     */
    protected $_helper;
   
    /**
     * @var Currency
     */
    protected $_currency;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Currency $currency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_currency = $currency;
    }

    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getOrder();
        $this->getSource();

        if (!$this->getSource()->getInsuranceFee()) {
            return $this;
        }
        $total = new DataObject(
            [
                'code' => 'insurance_fee',
                'value' => $this->getSource()->getInsuranceFee(),
                'label' => $this->_helper->getTitle(),
            ]
        );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');

        return $this;
    }
}
