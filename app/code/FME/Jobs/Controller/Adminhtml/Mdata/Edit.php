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
namespace FME\Jobs\Controller\Adminhtml\Mdata;

use Magento\Backend\App\Action;
 
class Edit extends \Magento\Backend\App\Action
{
    
    const ADMIN_RESOURCE = 'FME_Jobs::manage_mdata';
    
    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $model;
    
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \FME\Jobs\Model\Mdata $model,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->model = $model;
        parent::__construct($context);
    }
    
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Jobs::jobs_mdata')
            ->addBreadcrumb(__('META DATA'), __('META DATA'))
            ->addBreadcrumb(__('Manage Meta Data'), __('Manage Meta Data'));
        return $resultPage;
    }
        
    public function execute()
    {
        $id = $this->getRequest()->getParam('data_code');
        if ($id) {
            $this->model->load($id);
            if (!$this->model->getId()) {
                $this->messageManager
                ->addError(__('This data no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('jobs_mdata', $this->model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Meta Data') : __('New Meta Data'),
            $id ? __('Edit Meta Data') : __('New Meta Data')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Meta Data'));
        $resultPage->getConfig()->getTitle()
            ->prepend($this->model->getId() ? $this->model->getTitle() : __('New Meta Data'));
        return $resultPage;
    }
}
