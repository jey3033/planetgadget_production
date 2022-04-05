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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\ObjectManagerInterface;
  
class Job extends Template
{
    public $jobHelper;    
    protected $scopeConfig;
    protected $collectionFactory;
    protected $metaCollection;    
    protected $objectManager;
    protected $request;    
    protected $date;
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FME\Jobs\Model\ResourceModel\Job\
        CollectionFactory $collectionFactory,
        \FME\Jobs\Model\ResourceModel\Mdata\CollectionFactory $metaCollection,      
        \FME\Jobs\Helper\Job $jobHelper,        
        ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,        
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->metaCollection = $metaCollection;
        $this->jobHelper = $jobHelper;
        $this->context = $context;        
        $this->objectManager = $objectManager;        
        $this->request = $request;        
        $this->date = $date;        
        $this->_messageManager = $messageManager;
        $this->_storeManager =  $storemanager;
        parent::__construct($context);
    }
        
    public function _prepareLayout()
    {
        if ($this->jobHelper->isJobModuleEnable()) {
            $this->pageConfig->setKeywords($this->jobHelper->getJobPageMetakeywordSeo());
            $this->pageConfig->setDescription($this->jobHelper->getJobPageMetadescriptionSeo());
            $this->pageConfig->getTitle()->set($this->jobHelper->getJobPageTitleSeo());
            if ($this->getJobsCollection()) {
                $pager = $this->getLayout()->createBlock(
                    'Magento\Theme\Block\Html\Pager',
                    'fme.jobsffffffuuu.pageree'
                )->setAvailableLimit([5=>5,10=>10,15=>15])->setShowPerPage(true)->setCollection(
                    $this->getJobsCollection()
                );
                $this->setChild('pager', $pager);
                $this->getJobsCollection()->load();
            }
            return parent::_prepareLayout();
        }
    }
    
    public function getPagerHtml()
    {
         return $this->getChildHtml('pager');
    }
    
    public function getJobsCollection()
    {
        $date =  $this->getCurrDateTime();
        $date =  (array)$this->context->getLocaleDate()->date();
        $storeid=$this->_storeManager->getStore()->getStoreId();
        $date = $date['date'];        
        $filters = $this->getRequest()->getPostValue();       
        $collection = $this->collectionFactory->create();
        $collection = $collection->addFieldToFilter('is_active', 1)->addFieldToFilter('jobs_publish_date', array('lt' => $date))->addStoreViewFilter($storeid);
        if(!($this->jobHelper->getJobExpiredStatus())){
            $collection = $collection->addFieldToFilter('jobs_applyby_date', array('gt' => $date));
        }       
        if(!empty($filters)){
            if((!empty($filters['dept']))) {
            $collection = $collection
                    ->addFieldToFilter(['jobs_select_departments'],
                                    [['in' => $filters['dept']]]);
            }
            if(!empty($filters['loc'])){
                $collection = $collection
                ->addFieldToFilter(['jobs_location'],
                                [['in' => $filters['loc']]]);
            }
             if(!empty($filters['typ'])){
                $collection = $collection->addFieldToFilter(['jobs_job_type'],
                        [['in' => $filters['typ']]]);
            }
            $collection->setOrder('jobs_publish_date', 'DESC'); 
            $page = $this->getRequest()->getParam('p',1);
            $pageSize = $this->getRequest()->getParam('limit',5);
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);
            return $collection;
        }else{
            $collection->setOrder('jobs_publish_date', 'DESC');
            $page = $this->getRequest()->getParam('p',1);
            $pageSize = $this->getRequest()->getParam('limit',5);
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);
            return $collection;
        }
    }

    public function getSerializeFormData()
    {
        $filters = $this->getRequest()->getPostValue();        
        return json_encode($filters);
    }       

    public function getMetaCollection()
    {
        $collection = $this->metaCollection->create()->addFieldToFilter('data_status', 1);
        return $collection;
    }

    public function getDepartmentName($id)
    {
        $collection = $this->metaCollection->create()->addFieldToFilter('data_code', $id);
        $collection = $collection->getData();
        if($collection)
            {
                $collection = $collection[0]['data_name'];
                return $collection;
            }else{
                return;
            }
                
    }

    public function getLocation($id)
    {
        $collection = $this->metaCollection->create()->addFieldToFilter('data_code', $id);
        $collection = $collection->getData();
        if($collection)
        {
            $collection = $collection[0]['data_name'];
            return $collection;
        }else{
            return;
        }   
    }    
        
    public function getCurrDateTime()
    {
        $datewithoffset = $this->context->getLocaleDate()->date();
        $datewithoutoffset = $this->date->gmtDate();     
        return $datewithoffset;
     
    }
}
