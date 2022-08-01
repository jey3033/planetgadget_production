<?php

namespace Kemana\CustomerMembership\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Kemana\CustomerMembership\Helper\Data;

class CustomerGroupColumn extends Select
{
    protected $helper;

    public function __construct(
        Data                                    $helper,
        \Magento\Framework\View\Element\Context $context,
        array                                   $data = []
    )
    {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $customerGroups = $this->helper->getCustomerGroups();
        $options = [];

        foreach ($customerGroups as $group) {
            $options[] = ['label' => strtoupper($group->getCode()), 'value' => $group->getId()];
        }

        return $options;
    }
}
