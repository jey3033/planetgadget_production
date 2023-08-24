<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Blog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   kemana team <jakartateam@kemana.com>
 */

namespace Kemana\Blog\Block\Adminhtml\Widget;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Collection
 * @package Kemana\Blog\Model\ResourceModel\PostLike
 */
class Number extends Column
{
    /**
     * @param AbstractElement $element
     *
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $html = '<input type="text" name="' . $element->getName() . '" id="' . $element->getId() . '"
        class=" input-text admin__control-text required-entry _required validate-digits" value="' . $element->getValue() . '">';
        $element->setData('value', '');
        $element->setData('after_element_html', $html);

        return $element;
    }
}
