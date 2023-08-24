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

namespace Kemana\Blog\Block\Html;

use Magento\Framework\View\Element\Html\Link;
use Magento\Framework\View\Element\Template\Context;
use Kemana\Blog\Helper\Data;

/**
 * Class Footer
 * @package Kemana\Blog\Block\Html
 */
class Footer extends Link
{
    /**
     * @var Data
     */
    public $helper;

    /**
     * @var string
     */
    protected $_template = 'Kemana_Blog::html\footer.phtml';

    /**
     * Footer constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->helper->getBlogUrl('');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->helper->getBlogConfig('general/name') == "") {
            return __("Blog");
        }

        return $this->helper->getBlogConfig('general/name');
    }

    /**
     * @return string
     */
    public function getHtmlSiteMapUrl()
    {
        return $this->helper->getBlogUrl('sitemap');
    }
}
