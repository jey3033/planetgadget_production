<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Controller\Index;


/**
 * Class Detail
 *
 * @package Kemana\Promotion\Controller\Index
 */
class Detail extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Kemana\Promotion\Helper\Data
     */
    protected $promotionHelper;

    /**
     * @var \Kemana\Promotion\Block\Promotion\Promotion
     */
    protected $promotionBlock;

    /**
     * Detail constructor.
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Kemana\Promotion\Helper\Data $promotionHelper
     * @param \Kemana\Promotion\Block\Promotion\Promotion $promotionBlock
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Kemana\Promotion\Helper\Data $promotionHelper,
        \Kemana\Promotion\Block\Promotion\Promotion $promotionBlock
    ) {
        $this->pageFactory = $pageFactory;
        $this->promotionHelper = $promotionHelper;
        $this->promotionBlock = $promotionBlock;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->promotionHelper->promotionIsEnable()) {
            $this->_redirect('/');
        }

        $postData = $this->getRequest()->getPostValue();

        $resultPage = $this->pageFactory->create();

        $resultPage->getConfig()
            ->getTitle()
            ->set(($postData['meta_title']) ? : $postData['title']);

        $resultPage->getConfig()->setKeywords($postData['meta_keywords']);
        $resultPage->getConfig()->setDescription($postData['meta_description']);

        return $resultPage;
    }
}
