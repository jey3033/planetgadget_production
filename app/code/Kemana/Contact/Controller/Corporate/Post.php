<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Contact
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */
namespace Kemana\Contact\Controller\Corporate;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class Post extends \Magento\Framework\App\Action\Action
{

	protected $_pageFactory;
	/**
	* @var \Magento\Framework\App\Config\ScopeConfigInterface
	*/
	protected $scopeConfig;

	/**
	* Recipient email config path
	*/
	const XML_PATH_EMAIL_RECIPIENT = 'kemana_acceleratorbase/kemana_corporateinfo/recipient_email';
	const XML_PATH_EMAIL_TEMPLATE = 'kemana_acceleratorbase/kemana_corporateinfo/corporate_email_template';

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state
	)
	{
		$this->_pageFactory = $pageFactory;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->scopeConfig = $scopeConfig;
		return parent::__construct($context);
	}

	public function getReceipentEmail() {
     	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    	return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
    }

    public function getEmailTemplate() {
     	$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    	return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope);
    }

	public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }
        try {
            $this->sendEmail($this->validatedParams());
            $this->messageManager->addSuccessMessage(
                __('Thank you for your interest in the solutions we offer. We will contact you within 1x24 hours')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));   
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
        }
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
    }

     /**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
    	
        if (trim($request->getParam('name')) === '') {
            throw new LocalizedException(__('Enter the Name and try again.'));
        }
        if (trim($request->getParam('desired')) === '') {
            throw new LocalizedException(__('Enter the comment and try again.'));
        }

        if (trim($request->getParam('telephone')) === '') {
            throw new LocalizedException(__('Enter the telephone and try again.'));
        }

        if (trim($request->getParam('intrested')) === '') {
            throw new LocalizedException(__('Enter the intrested and try again.'));
        }

        if (trim($request->getParam('companyname')) === '') {
            throw new LocalizedException(__('Enter the companyname and try again.'));
        }
        
        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }

        if (trim($request->getParam('companyfield')) === '') {
            throw new LocalizedException(__('Enter the companyfield and try again.'));
        }

        if (trim($request->getParam('job')) === '') {
            throw new LocalizedException(__('Enter the corporatejob and try again.'));
        }

        return $request->getParams();
    }

    public function sendEmail($templateVars)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
 
            $from = ['email' => $templateVars['email'], 'name' => $templateVars['name']];
            $this->inlineTranslation->suspend();
 
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($this->getEmailTemplate(), $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($this->getReceipentEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
         	throw new LocalizedException(__($e->getMessage()));   
        }
    }
}