<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model\ResourceModel\SliderCron;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Kemana\Banner\Model\ResourceModel\SliderCron
 */
class Collection extends AbstractCollection
{
    /**
     * Function _construct
     */
    protected function _construct()
    {
        $this->_init('Kemana\Banner\Model\SliderCron', 'Kemana\Banner\Model\ResourceModel\SliderCron');
    }
}
