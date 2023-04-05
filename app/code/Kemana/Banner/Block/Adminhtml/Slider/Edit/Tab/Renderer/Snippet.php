<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Slider\Edit\Tab\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Snippet
 * @package Kemana\Banner\Block\Adminhtml\Slider\Edit\Tab\Renderer
 */
class Snippet extends AbstractElement
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $sliderId = '1';
        $html = '<ul class="banner-location-display"><li><span>';
        $html .= __('Add Widget with name "Banner Slider widget" and set "Slider Id" for it.');
        $html .= '</span></li><li><span>';
        $html .= __('CMS Page/Static Block');
        $html .= '</span><code>{{block class="Kemana\Banner\Block\Widget" slider_id="' . $sliderId . '"}}</code><p>';
        $html .= __('You can paste the above block of snippet into any page in Magento 2 and set SliderId for it.');
        $html .= '</p></li><li><span>';
        $html .= __('Template .phtml file');
        $html .= '</span><code>' . $this->_escaper->escapeHtml('<?= $block->getLayout()->createBlock("Kemana\Banner\Block\Widget::class")->setSliderId(' . $sliderId . ')->toHtml();?>') . '</code><p>';
        $html .= __('Open a .phtml file and insert where you want to display Banner Slider.');
        $html .= '</p></li></ul>';

        return $html;
    }
}
