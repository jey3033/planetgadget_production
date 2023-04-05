<?php
/**
 * Kemana_Banner
 * @author Harsha Amaraweera <hamaraweera@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Snippet
 * @package Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Renderer
 */
class Snippet extends AbstractElement
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $str = "&lt;div class=\"full-width-banner main-home-slider\"&gt;<br/>
    &nbsp &lt;div class=\"banner-img\"&gt;<br/>
        &nbsp &nbsp &lt;img class=\"desktop\" src=\"{{media url=\"wysiwyg/main-banner-slider.png\"}}\" alt=\"\"&gt;<br/>
        &nbsp &nbsp &lt;img class=\"mobile\" src=\"{{media url=\"wysiwyg/main-banner-slider-m.png\"}}\" alt=\"\"&gt;<br/>
    &nbsp &lt;/div&gt;<br/>
    &nbsp &lt;div class=\"banner-description color-white center\"&gt;<br/>
        &nbsp &nbsp &lt;div class=\"banner-description-container\"&gt;<br/>
             &nbsp &nbsp &nbsp &lt;h1&gt;<-- Banner Title goes here -->&lt;/h1&gt;<br/>
             &nbsp &nbsp &nbsp &lt;p&gt;<-- Banner description goes here -->&lt;/p&gt;<br/>
             &nbsp &nbsp &nbsp &lt;div class=\"actions-toolbar\"&gt;<br/>
                  &nbsp &nbsp &nbsp &nbsp &lt;div class=\"primary\"&gt;<br/>
                       &nbsp &nbsp &nbsp &nbsp &nbsp &lt;a class=\"action primary shop-now\" href=\"#\">SHOP NOW&lt;/a&gt;<br/>
                  &nbsp &nbsp &nbsp &nbsp &lt;/div&gt;<br/>
             &nbsp &nbsp &nbsp &lt;/div&gt;<br/>
        &nbsp &nbsp &lt;/div&gt;<br/>
   &nbsp &lt;/div&gt;<br/>
&lt;/div&gt;";

        return  "<span class='banner-location-display'><code><p>".$str."</p></code></span>";
    }
}
