<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_Jobs
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Jobs\Block\Adminhtml;

class Mdata extends \Magento\Backend\Block\Widget\Container
{
    
    protected $_typeFactory;    
    protected $_productFactory;
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \FME\Jobs\Model\Job $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_productFactory = $productFactory;
        $this->_typeFactory = $typeFactory;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_product',
            'label' => __('Add Meta Data'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);
        return parent::_prepareLayout();
    }
    
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [];
        $types = $this->_typeFactory->getTypes();
        uasort(
            $types,
            function ($elementOne, $elementTwo) {
                return ($elementOne['id'] < $elementTwo['id']) ? -1 : 1;
            }
        );
        foreach ($types as $typeId => $type) {
            $splitButtonOptions[$typeId] = [
                'label' => __($type['type_name']),
                'onclick' => "setLocation('" . $this->_getProductCreateUrl($type['id'],$type['type_name']) . "')",
                
            ];
        }
        return $splitButtonOptions;
    }
    
    protected function _getProductCreateUrl($type,$typeName)
    {
        return $this->getUrl(
            'jobs/*/new',
            ['set' => $type, 'type' => $typeName]
        );
    }
    
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
