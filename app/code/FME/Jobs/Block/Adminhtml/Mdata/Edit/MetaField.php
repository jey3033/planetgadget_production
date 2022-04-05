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

namespace FME\Jobs\Block\Adminhtml\Mdata\Edit;

class MetaField extends \Magento\Backend\Block\Template
{
    protected $_template = 'FME_Jobs::/mdata/meta_field.phtml';
    protected $blockGrid;
    protected $registry;
    protected $jsonEncoder;
    protected $_productFactory;
    protected $_eventFactory;
    public $_storeAdminHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \FME\Jobs\Model\Job $eventFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        
        $this->_eventFactory = $eventFactory;
        parent::__construct($context);
    }
    
    public function getMetaId()
    {
        $id = $this->getRequest()->getParam('set');
        return $id;
    }

    public function getDatCodeEditId()
    {
        $id = $this->getRequest()->getParam('data_code');
         if($id){
                $mediaobj = $this->_eventFactory->getTypesCode($id);
                $mediaobj = $mediaobj['0']['type_code'];         
            return $mediaobj;
          }
    }

    public function getMetaName()
    {
        $id = $this->getRequest()->getParam('type');
        return $id;
    }

    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    public function getMetaCollection()
    {
        return  $this->_eventFactory->getTypes();
    }
}
