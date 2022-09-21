<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_StockAvailabilityPopup
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\StockAvailabilityPopup\Block\Product;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Content
 */
class View extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param array $data
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, array $data = [])
    {
        parent::__construct($context, $arrayUtils, $data);
    }

}
