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

namespace Kemana\Blog\Block\Adminhtml\Renderer;

/**
 * Class Image
 * @package Kemana\Blog\Block\Adminhtml\Renderer
 */
class Image extends \Kemana\Blog\Block\Adminhtml\Renderer\ImageData
{
    /**
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = __('Delete Image');
            $html .= '<span class="delete-image">';
            $html .= '<input style="margin: auto;" type="checkbox"' .
                ' name="' .
                $this->getName() .
                '[delete]" value="1" class="checkbox"' .
                ' id="' .
                $this->getHtmlId() .
                '_delete"' .
                ($this->getDisabled() ? ' disabled="disabled"' : '') .
                '/>';
            $html .= '<label for="' . $this->getHtmlId() .
                '_delete"' . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' .
                $label .
                '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }
        $html .= '<style>#author_image_image, #post_image_image{ position: relative;top: 6px }</style>';

        return $html;
    }
}
