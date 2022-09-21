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
use FME\Jobs\Model\Applications;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'FME_Jobs::manage_applications';
    protected $dataProcessor;
    protected $dataPersistor;
    protected $model;

    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        Applications $model,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->model = $model;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Applications::STATUS_ENABLED;
            }
            if (empty($data['app_id'])) {
                $data['app_id'] = null;
            }
            $id = $this->getRequest()->getParam('app_id');
            if ($id) {
                $this->model->load($id);
            }
            $this->model->setData($data);
            $this->_eventManager->dispatch(
                'jobs_applications_prepare_save',
                ['Applications' => $this->model, 'request' => $this->getRequest()]
            );
            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['app_id' => $this->model->getId(), '_current' => true]);
            }
            try {
                $this->model->save();
                $this->messageManager->addSuccess(__('You saved the applications.'));
                $this->dataPersistor->clear('jobs');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['app_id' => $this->model->getId(),
                         '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the applications.'));
            }
            $this->dataPersistor->set('jobs', $data);
            return $resultRedirect->setPath('*/*/edit', ['app_id' => $this->getRequest()->getParam('app_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
