<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class SliderCron
 * @package Kemana\Banner\Model
 */
class SliderCron extends AbstractModel
{
    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init('Kemana\Banner\Model\ResourceModel\SliderCron');
    }
}
