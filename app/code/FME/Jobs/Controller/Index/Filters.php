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
namespace FME\Jobs\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
class Filters extends \Magento\Framework\App\Action\Action
{
    
    public function __construct(        
        \Magento\Framework\App\Action\Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;       
        $this->resultJsonFactory = $resultJsonFactory; 
        parent::__construct($context);
    }

    public function execute()
    {        
        $resultPage = $this->resultPageFactory->create();
        $resultJsonFactory = $this->resultJsonFactory->create();
        $filters = $this->getRequest()->getPostValue();
        $block = $resultPage->getLayout()
                ->createBlock('FME\Jobs\Block\Job')
                ->setTemplate('FME_Jobs::jobs/filters.phtml')                
                ->toHtml();        
        $resultJsonFactory->setData($block);
        return $resultJsonFactory;  
    }
}
