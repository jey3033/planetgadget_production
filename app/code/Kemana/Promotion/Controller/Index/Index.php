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
 * Class Index
 *
 * @package Kemana\Promotion\Controller\Index
 */
class Index extends \Magento\Framework\App\Action\Action
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
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Kemana\Promotion\Helper\Data $promotionHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Kemana\Promotion\Helper\Data $promotionHelper
    ) {
        $this->pageFactory = $pageFactory;
        $this->promotionHelper = $promotionHelper;

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

        $resultPage = $this->pageFactory->create();

        $resultPage->getConfig()->setPageLayout('1column');

        $resultPage->getConfig()
            ->getTitle()
            ->set(__($this->promotionHelper->getLandingPageTitle() ?
                $this->promotionHelper->getLandingPageTitle() : 'Promotions'));

        return $resultPage;
    }
}
