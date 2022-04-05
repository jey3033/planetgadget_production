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
namespace FME\Jobs\Controller\Adminhtml\Job;

use Magento\Backend\App\Action;
 
class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'FME_Jobs::manage_job';
    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $model;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \FME\Jobs\Model\Job $model,
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
        $resultPage->setActiveMenu('FME_Jobs::jobs_job')
            ->addBreadcrumb(__('Job'), __('Job'))
            ->addBreadcrumb(__('Manage Job'), __('Manage Job'));
        return $resultPage;
    }
        
    public function execute()
    {
        $id = $this->getRequest()->getParam('jobs_id');
        if ($id) {
            $this->model->load($id);
            if (!$this->model->getId()) {
                $this->messageManager
                ->addError(__('This job no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('jobs_job', $this->model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Job') : __('New Job'),
            $id ? __('Edit Job') : __('New Job')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Jobs'));
        $resultPage->getConfig()->getTitle()
            ->prepend($this->model->getId() ? $this->model->getJobsTitle() : __('New Job'));
        return $resultPage;
    }
}
