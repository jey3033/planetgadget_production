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

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;

/**
 * Class Delete
 *
 * @package Kemana\Promotion\Controller\Adminhtml\Promotion
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Kemana_Promotion::promotion_delete';

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
     * @var \Kemana\Promotion\Api\PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * @var string
     */
    protected $cacheTypes = 'full_page';

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Kemana\Promotion\Model\PromotionFactory $promotionFactory
     * @param \Kemana\Promotion\Api\PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Kemana\Promotion\Model\PromotionFactory $promotionFactory,
        \Kemana\Promotion\Api\PromotionRepositoryInterface $promotionRepository,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    )
    {
        $this->promotionRepository = $promotionRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->promotionFactory = $promotionFactory;
        $this->cacheTypeList = $cacheTypeList;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('promotion_id');

        if ($id) {
            try {
                $this->promotionRepository->deleteById($id);
                $this->cacheTypeList->invalidate($this->cacheTypes);

                $this->messageManager->addSuccessMessage(__('You deleted a promotion.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {

                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['promotion_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find this promotion to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
