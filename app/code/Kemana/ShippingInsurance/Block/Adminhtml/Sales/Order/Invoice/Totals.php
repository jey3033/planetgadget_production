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

namespace Kemana\ShippingInsurance\Block\Adminhtml\Sales\Order\Invoice;

use Kemana\ShippingInsurance\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Invoice;

class Totals extends Template
{

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Invoice
     */
    protected $_invoice = null;

    /**
     * @var DataObject
     */
    protected $_source;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getInvoice();
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
