<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Jobs
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Jobs\Block\Adminhtml\Applications\Edit;

/**
 * Class AdditionalFileField
 */
class AdditionalFileField extends \Magento\Backend\Block\Template
{
    
    /**
     * @var Context
     */
    protected $_contextMgr;

    /**
     * @var Job
     */
    protected $_eventFactory;
    
    /**
     * @param Context $context
     * @param Job $_eventFactory
     */
    public function __construct(        
        \Magento\Backend\Block\Template\Context $context,
        \FME\Jobs\Model\Job $eventFactory        
    ) {
        
        $this->_eventFactory = $eventFactory;       
        $this->_contextMgr = $context;
        
        parent::__construct($context);
    }

    /**
     * Get Additional file path coverletterfile, idcardfile, educationcertfile
     * 
     * @param $fieldpath
     * @return string
     */
    public function getAdditionalFilePath($fieldpath)
    {
        $id = $this->getRequest()->getParam('app_id');
        $urlFilePath = '';
        if ($id) {
            $mediaobj = $this->_eventFactory->getCvDownloadLink($id);
            if (!empty($mediaobj = $mediaobj['0'][$fieldpath])) {
                $media_url = $this->_contextMgr->getStoreManager()->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $urlFilePath = $media_url.'fme_jobs'.$mediaobj;  
            }
            return $urlFilePath;
        }
    }
}
