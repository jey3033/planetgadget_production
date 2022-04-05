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
use FME\Jobs\Model\Job;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    
    const ADMIN_RESOURCE = 'FME_Jobs::manage_job';
    protected $dataProcessor;
    protected $dataPersistor;
    protected $model;

    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        Job $model,
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
                $data['is_active'] = Job::STATUS_ENABLED;
            }
            if (empty($data['jobs_id'])) {
                $data['jobs_id'] = null;
            }
            $id = $this->getRequest()->getParam('jobs_id');
            if ($id) {
                $this->model->load($id);
            }
            $data['jobs_url_key'] = str_replace('/', '-', $data['jobs_url_key']);
            $data['jobs_url_key'] = str_replace(' ', '-', $data['jobs_url_key']);
            $data['jobs_url_key'] = preg_replace("![^a-z0-9]+!i", "-", $data['jobs_url_key']);
            if ($data['use_config_email'] == 1) {
                $data['notification_email_receiver'] = '';
            }
            $this->model->setData($data);
            $this->_eventManager->dispatch(
                'jobs_job_prepare_save',
                ['job' => $this->model, 'request' => $this->getRequest()]
            );
            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['jobs_id' => $this->model->getId(), '_current' => true]);
            }
            try {
                $this->model->save();
                $this->messageManager->addSuccess(__('You saved the job.'));
                $this->dataPersistor->clear('jobs');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['jobs_id' => $this->model->getId(),
                         '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the job.'));
            }
            $this->dataPersistor->set('jobs', $data);
            return $resultRedirect->setPath('*/*/edit', ['jobs_id' => $this->getRequest()->getParam('jobs_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
