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

namespace Kemana\Blog\Block;

use Kemana\Blog\Helper\Data as HelperData;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Frontend
 *
 * @package Kemana\Blog\Block
 */
class Design extends Template
{
    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var ThemeProviderInterface
     */
    protected $_themeProvider;

    /**
     * Design constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     * @param ThemeProviderInterface $_themeProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        ThemeProviderInterface $_themeProvider,
        array $data = []
    ) {
        $this->helperData     = $helperData;
        $this->_themeProvider = $_themeProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return HelperData
     */
    public function getHelper()
    {
        return $this->helperData;
    }

    /**
     * Get Current Theme Name Function
     * @return string
     */
    public function getCurrentTheme()
    {
        $themeId = $this->helperData->getDesignThemeId();

        /** @var $theme ThemeInterface */
        $theme = $this->_themeProvider->getThemeById($themeId);

        return $theme->getCode();
    }
}
