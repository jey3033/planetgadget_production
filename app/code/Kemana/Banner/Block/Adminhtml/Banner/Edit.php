<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block\Adminhtml\Banner;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Kemana\Banner\Model\Banner;

/**
 * Class Edit
 * @package Kemana\Banner\Block\Adminhtml\Banner
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Initialize Banner edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'banner_id';
        $this->_blockGroup = 'Kemana_Banner';
        $this->_controller = 'adminhtml_banner';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Banner'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Banner'));
    }

    /**
     * Retrieve text for header element depending on loaded Banner
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Banner $banner */
        $banner = $this->getBanner();
        if ($banner->getId()) {
            return __("Edit Banner '%1'", $this->escapeHtml($banner->getName()));
        }

        return __('New Banner');
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->coreRegistry->registry('bannerslider_banner');
    }
}
