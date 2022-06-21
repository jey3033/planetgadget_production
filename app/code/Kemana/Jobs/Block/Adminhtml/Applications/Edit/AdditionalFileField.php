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
 * Class CoverLetterField
 */
class AdditionalFileField extends \Magento\Backend\Block\Template
{
    
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
        $urlCv = '';
        if ($id) {
            $mediaobj = $this->_eventFactory->getCvDownloadLink($id);
            $mediaobj = $mediaobj['0'][$fieldpath];
            if (!empty($mediaobj)) {
                $media_url = $this->_contextMgr->getStoreManager()->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $urlCv = $media_url.'fme_jobs'.$mediaobj;  
            }
        return $urlCv;
        }
    }
}
