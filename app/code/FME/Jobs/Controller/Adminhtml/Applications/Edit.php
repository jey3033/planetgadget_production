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
namespace FME\Jobs\Controller\Adminhtml\Applications;

use Magento\Backend\App\Action;
 
class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'FME_Jobs::manage_applications';
    protected $_coreRegistry;
    protected $resultPageFactory;
    protected $model;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \FME\Jobs\Model\Applications $model,
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
        $resultPage->setActiveMenu('FME_Jobs::jobs_applications')
            ->addBreadcrumb(__('APPLICATIONS'), __('APPLICATIONS'))
            ->addBreadcrumb(__('Manage Applications'), __('Manage Applications'));
        return $resultPage;
    }
        
    public function execute()
    {
        $id = $this->getRequest()->getParam('app_id');
        if ($id) {
            $this->model->load($id);
            if (!$this->model->getId()) {
                $this->messageManager
                ->addError(__('This application no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('jobs_applications', $this->model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Applications') : __('New Applications'),
            $id ? __('Edit Applications') : __('New Applications')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Jobs'));
        $resultPage->getConfig()->getTitle()
            ->prepend($this->model->getId() ? $this->model->getFullname() : __('New Applications'));
        return $resultPage;
    }
}
