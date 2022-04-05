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
use FME\Jobs\Model\Mdata;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'FME_Jobs::manage_mdata';
    protected $dataProcessor;
    protected $dataPersistor;
    protected $model;

    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        Mdata $model,
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
                $data['is_active'] = Article::STATUS_ENABLED;
            }
            if (empty($data['data_code'])) {
                $data['data_code'] = null;
            }
            $id = $this->getRequest()->getParam('data_code');
            if ($id) {
                $this->model->load($id);
            }
            $this->model->setData($data);
            $this->_eventManager->dispatch(
                'jobs_mdata_prepare_save',
                ['mdata' => $this->model, 'request' => $this->getRequest()]
            );
            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['data_code' => $this->model->getId(), '_current' => true]);
            }
            try {
                $this->model->save();
                $this->messageManager->addSuccess(__('You saved the data.'));
                $this->dataPersistor->clear('jobs');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['data_code' => $this->model->getId(),
                         '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }
            $this->dataPersistor->set('jobs', $data);
            return $resultRedirect->setPath('*/*/edit', ['data_code' => $this->getRequest()->getParam('data_code')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
