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
namespace FME\Jobs\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;

class Detail extends Template
{

    protected $collectionFactory;   
    protected $objectManager;     
    public $articlesHelper;   
    protected $_registry = null;    
    protected $job;

    public function __construct(
        \FME\Jobs\Model\ResourceModel\Job\CollectionFactory $collectionFactory,
        \FME\Jobs\Block\Job $job,
        \FME\Jobs\Helper\Job $helper,
        \Magento\Framework\Registry $coreRegistry,
        ObjectManagerInterface $objectManager,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        array $data = []
    ) {
        
        $this->collectionFactory = $collectionFactory;        
        $this->objectManager = $objectManager;
        $this->articlesHelper = $helper;
        $this->context = $context;        
        $this->_registry = $coreRegistry;        
        $this->job = $job;
        $this->_storeManager =  $storemanager;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        $params = $this->getRequest()->getParams();

        $prefix = $params['id'];
        if ($this->articlesHelper->isJobModuleEnable()) {
             $this->pageConfig->getTitle()->set($this->getArticleDetailTitle($prefix, 'job_page_title'));
            $this->pageConfig->setKeywords($this->getArticleDetailTitle($prefix, 'job_meta_keywords'));
            $this->pageConfig->setDescription($this->getArticleDetailTitle($prefix, 'job_meta_description'));
  
            return parent::_prepareLayout();
        }
    }    

    public function getJobDetail()
    {
        $params = $this->getRequest()->getParams();
        $prefix = $params['id'];
        $storeid=$this->_storeManager->getStore()->getStoreId();
        $collection = $this->collectionFactory->create()->addFieldToFilter('jobs_url_key', $prefix)->addStoreViewFilter($storeid);
        return $collection;
    }

    public function getDetailPageDepartment($id)
    {
        return $this->job->getDepartmentName($id);
    }

    public function getArticleDetailTitle($eventId, $earg)
    {
        $collection = $this->collectionFactory->create()->addFieldToFilter('jobs_url_key', $eventId);
        $collection = $collection->getData();
        if ($collection) {
            $collection = $collection[0][$earg];
        }
            return $collection;
    }    

    public function getAddApplicationUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_LINK
        ).'job/index/application';
    }

    public function getCurrDateTime()
    {
      $datewithoffset = $this->context->getLocaleDate()->date();      
        return $datewithoffset;     
    }
}
