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

namespace Kemana\Promotion\Controller\Adminhtml\Promotion;

use Kemana\Promotion\Api\PromotionRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Edit
 *
 * @package Kemana\Promotion\Controller\Adminhtml\Promotion
 */
class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Kemana_Promotion::promotion_save';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Kemana\Promotion\Model\PromotionFactory
     */
    protected $promotionFactory;

    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Kemana\Promotion\Model\PromotionFactory $promotionFactory
     * @param PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Kemana\Promotion\Model\PromotionFactory $promotionFactory,
        PromotionRepositoryInterface $promotionRepository

    ) {
        $this->promotionRepository = $promotionRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->promotionFactory = $promotionFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('promotion_id');

        $model = $this->promotionFactory->create();

        if ($id) {

            $model = $this->promotionRepository->getById($id);
            if (!$model->getId()) {

                $this->messageManager->addErrorMessage(__('This promotion no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('kemana_promotion', $model);

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Kemana_Promotion::promotion');
        $resultPage->getConfig()->getTitle()->prepend(__('Promotion'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Promotion') : __('New Promotion'));

        return $resultPage;
    }
}
