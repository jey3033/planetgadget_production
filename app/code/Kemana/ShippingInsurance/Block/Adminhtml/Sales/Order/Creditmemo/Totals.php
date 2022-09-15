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
namespace Kemana\ShippingInsurance\Block\Adminhtml\Sales\Order\Creditmemo;

use Kemana\ShippingInsurance\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Creditmemo;

class Totals extends Template
{
    /**
     * @var Creditmemo
     */
    protected $_creditmemo = null;

    /**
     * @var DataObject
     */
    protected $_source;

    /**
     * @var Data
     */
    protected $_helper;

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

    /**
     * @return DataObject
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $this->getCreditmemo();
        $this->getSource();

        if (!$this->getSource()->getInsuranceFee()) {
            return $this;
        }

        $insuranceFee = new DataObject(
            [
                'code' => 'insurance_fee',
                'strong' => false,
                'value' => $this->getSource()->getInsuranceFee(),
                'label' => $this->_helper->getTitle(),
            ]
        );

        $this->getParentBlock()->addTotalBefore($insuranceFee, 'grand_total');

        return $this;
    }
}
