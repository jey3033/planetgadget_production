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

namespace Kemana\Jobs\Plugin\Jobs\Controller\Index;

use  FME\Jobs\Model\Applications;
use  Magento\Framework\App\Filesystem\DirectoryList;
use  Magento\Framework\Exception\LocalizedException;
use  Psr\Log\LoggerInterface;
use  Magento\Framework\App\ObjectManager;
use  Magento\Framework\App\Config\ScopeConfigInterface;
use  Magento\Store\Model\ScopeInterface;

/**
 * Class Application
 */
class Application extends \FME\Jobs\Controller\Index\Application
{
    
    /**
     * override existing saving application
     * file extension accepted files and added another 3 file upload field Cover Letter, ID Card, Education Certificate
     */
    public function execute()
    {
      
        try {
    
            $files = $this->getRequest()->getFiles();
            $files = (array)$files;
            
            $saveData = [];
            if ($files!='')
            {

                foreach ($files as $key => $value) {

                    if ($value['error'] == 1 && $value['size'] == 0) {
                        $this->messageManager->addErrorMessage(__('File size too large to upload'));
                        $this->_redirect('*/*/');
                        return;
                    }
                    $uploaderFactory = $this->uploaderFactory->create(['fileId' => $key]);
                    $uploaderFactory->setAllowedExtensions(['pdf', 'jpg', 'png','pdf', 'docx', 'doc', 'zip', 'rar']);
                    $file_ext = pathinfo($value['name'], PATHINFO_EXTENSION);
                    if (!$uploaderFactory->checkAllowedExtension($file_ext)) {
                        $this->messageManager->addErrorMessage(__('File type '.$value['name'].' not supported'));
                        $this->_redirect('*/*/');
                        return;
                    }
                    $imageAdapter = $this->adapterFactory->create();
                    $uploaderFactory->setAllowRenameFiles(true);
                    $uploaderFactory->setFilesDispersion(true);
                    $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $destinationPath = $mediaDirectory->getAbsolutePath('fme_jobs');
                    $result = $uploaderFactory->save($destinationPath);
                    $imagepath = $result['file'];
                    $saveData[$key] = $imagepath;
                   
                }
            }
        } catch (\Exception $e) {
                
                $this->messageManager->addErrorMessage($e->getMessage());  
                $this->_redirect('*/*/');
                return;
        } 
        $data = $this->getRequest()->getPostValue();
        $jobdata=$this->_mymoduleHelper->getSingleJobById($data['jobs_id']);
        /*----Email To Applicant------*****/
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);              
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_mymoduleHelper->getSenderEmailTemplate(), $storeScope)
            ->setTemplateOptions(
            [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId(),
            ]
            )->setTemplateVars(['fieldsData' => $postObject])
            ->setFrom($this->_mymoduleHelper->getSenderEmail(), $storeScope)
            ->addTo($data['email'], $storeScope)
            ->getTransport();
        try {
            $transport->sendMessage();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /******Email To Applicant End******/
        $jobdata1=$jobdata->getData();
        if ($this->_mymoduleHelper->getReceiverEmail() == 'sales') {
            $to=$this->scopeConfig->getValue('trans_email/ident_sales/email',ScopeInterface::SCOPE_STORE);
        }
        if ($this->_mymoduleHelper->getReceiverEmail() == 'general') {
            $to=$this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
        }
        if ($this->_mymoduleHelper->getReceiverEmail() == 'support') {
            $to=$this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        }
        if ($this->_mymoduleHelper->getReceiverEmail() == 'custom1') {
            $to=$this->scopeConfig->getValue('trans_email/ident_custom1/email',ScopeInterface::SCOPE_STORE);
        }
        if ($this->_mymoduleHelper->getReceiverEmail() == 'custom2') {
            $to=$this->scopeConfig->getValue('trans_email/ident_custom2/email',ScopeInterface::SCOPE_STORE);
        }
        if ($jobdata1[0]['use_config_email'] == 1 && $jobdata1[0]['use_config_template'] == 1) {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);              
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->_mymoduleHelper->getSenderEmailTemplate(), $storeScope)
                ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
                ]
                )->setTemplateVars(['fieldsData' => $postObject])
                ->setFrom($this->_mymoduleHelper->getSenderEmail(), $storeScope)
                ->addTo($to)
                ->getTransport();
            try {
                $transport->sendMessage();
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else if ($jobdata1[0]['use_config_email'] != 1 && $jobdata1[0]['use_config_template'] == 1) {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);              
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->_mymoduleHelper->getSenderEmailTemplate(), $storeScope)
                ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
                ]
                )->setTemplateVars(['fieldsData' => $postObject])
                ->setFrom($this->_mymoduleHelper->getSenderEmail(), $storeScope)
                ->addTo($jobdata1[0]['notification_email_receiver'])
                ->getTransport();
            try {
                $transport->sendMessage();
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else if ($jobdata1[0]['use_config_email'] == 1 && $jobdata1[0]['use_config_template'] != 1) {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);              
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($jobdata1[0]['email_notification_temp'], $storeScope)
                ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
                ]
                )->setTemplateVars(['fieldsData' => $postObject])
                ->setFrom($this->_mymoduleHelper->getSenderEmail(), $storeScope)
                ->addTo($to)
                ->getTransport();
            try {
                $transport->sendMessage();
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);              

            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($jobdata1[0]['email_notification_temp'], $storeScope)
                ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
                ]
                )->setTemplateVars(['fieldsData' => $postObject])
                ->setFrom($this->_mymoduleHelper->getSenderEmail(), $storeScope)
                ->addTo($jobdata1[0]['notification_email_receiver'])
                ->getTransport();
            try {
                $transport->sendMessage();
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

            }
        }
        foreach ($data as $key=>$val)
        {
            $saveData[$key] = $val;
        }        
        
        $this->model->setData($saveData);
        $this->model->save();
        $this->messageManager->addSuccess(__('Your Application has been submitted.'));
        $this->_redirect('*/*/');
        return;
    }
}
