<?php

namespace Kemana\CustomerMembership\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Kemana\CustomerMembership\Block\Adminhtml\Form\Field\CustomerGroupColumn;

class Levels extends AbstractFieldArray
{
    /**
     * @var CustomerGroupColumn
     */
    private $customerGroupRenderer;

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('from_sale', ['label' => __('From'), 'class' => 'required-entry']);
        $this->addColumn('to_sale', ['label' => __('To'), 'class' => 'required-entry']);
        $this->addColumn('customer_group', [
            'label' => __('Membership Level - Customer Group'),
            'renderer' => $this->getCustomerGroupsRenderer()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $customerGroup = $row->getCustomerGroup();
        if ($customerGroup !== null) {
            $options['option_' . $this->getCustomerGroupsRenderer()->calcOptionHash($customerGroup)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return \Kemana\CustomerMembership\Block\Adminhtml\Form\Field\CustomerGroupColumn|\Magento\Framework\View\Element\BlockInterface
     * @throws LocalizedException
     */
    private function getCustomerGroupsRenderer()
    {
        if (!$this->customerGroupRenderer) {
            $this->customerGroupRenderer = $this->getLayout()->createBlock(
                CustomerGroupColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->customerGroupRenderer;
    }

}
