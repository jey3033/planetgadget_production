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
 * @category    FME
 * @package     FME_Jobs
 * @author      Dara Baig  (support@fmeextensions.com)
 * @copyright   Copyright (c) 2018 FME (http://fmeextensions.com/)
 * @license     https://fmeextensions.com/LICENSE.txt
 */

namespace FME\Jobs\Block\Adminhtml\Applications\Edit;

class CvField extends \Magento\Backend\Block\Template
{
    
    protected $_template = 'FME_Jobs::/mdata/cv_field.phtml';    
    protected $blockGrid;   
    protected $_eventFactory;   
    
    public function __construct(        
        \Magento\Backend\Block\Template\Context $context,
        \FME\Jobs\Model\Job $eventFactory        
    ) {
        
        $this->_eventFactory = $eventFactory;       
        $this->_contextMgr = $context;
        parent::__construct($context);
    }
    
    public function getMetaId()
    {
        $id = $this->getRequest()->getParam('app_id');        
        return $id;
    }

    public function getDatCodeEditId()
    {
        $id = $this->getRequest()->getParam('app_id');
         if($id){
                $mediaobj = $this->_eventFactory->getCvDownloadLink($id);
                $mediaobj = $mediaobj['0']['cvfile'];
                $media_url = $this->_contextMgr->getStoreManager()->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
             $urlCv = $media_url.'fme_jobs'.$mediaobj;  

            return $urlCv;
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
