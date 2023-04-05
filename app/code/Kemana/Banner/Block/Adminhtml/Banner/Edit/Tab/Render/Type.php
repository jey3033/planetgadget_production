<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Type
 * @package Kemana\Banner\Block\Adminhtml\Banner\Edit\Tab\Render
 */
class Type extends AbstractRenderer
{
    /**
     * Render banner type
     *
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $type = $row->getData($this->getColumn()->getIndex());
        switch ($type) {
            case 0:
                $type = 'Image';
                break;
            case 1:
                $type = 'Advanced';
                break;
        }

        return $type;
    }
}
