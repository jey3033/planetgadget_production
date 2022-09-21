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

use Kemana\Promotion\Model\PromotionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use \Kemana\Promotion\Api\PromotionRepositoryInterface;
use \Kemana\Promotion\Api\Data\PromotionInterface;
use \Kemana\Promotion\Helper\Data;
use \Magento\Framework\App\Cache\TypeListInterface;

/**
 * Class Save
 *
 * @package Kemana\Promotion\Controller\Adminhtml\Promotion
 */
class Save extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Kemana_Promotion::promotion_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PromotionFactory
     */
    protected $promotionFactory;

    /**
     * @var PromotionRepositoryInterface
     */
    protected $promotionRepository;

    /**
     * @var PromotionInterface
     */
    protected $promotionInterface;

    /**
     * @var Data
     */
    protected $promotionHelper;

    /**
     * @var string
     */
    protected $cacheTypes = 'full_page';

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Save constructor.
     *
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param PromotionFactory $promotionFactory
     * @param PromotionRepositoryInterface $promotionRepository
     * @param PromotionInterface $promotionInterface
     * @param Data $promotionHelper
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        PromotionFactory $promotionFactory,
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotionInterface,
        Data $promotionHelper,
        TypeListInterface $cacheTypeList
    ) {
        $this->promotionInterface = $promotionInterface;
        $this->promotionRepository = $promotionRepository;
        $this->dataPersistor = $dataPersistor;
        $this->promotionFactory = $promotionFactory;
        $this->promotionHelper = $promotionHelper;
        $this->cacheTypeList = $cacheTypeList;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {

            // Process landing image
            if (isset($data['landing_image'][0])) {
                $data['landing_image'] = 'promotion/' . $data['landing_image'][0]['name'];
            } else {
                $data['landing_image'] = null;
            }

            // Process landing image mobile
            if (isset($data['landing_image_mobile'][0])) {
                $data['landing_image_mobile'] = 'promotion/' . $data['landing_image_mobile'][0]['name'];
            } else {
                $data['landing_image_mobile'] = null;
            }

            if (empty($data['promotion_id'])) {
                $data['promotion_id'] = null;
            }

            if (is_array($data['stores'])) {
                $data['stores'] = implode(",", $data['stores']);
            }

            // Validate identifier
            $identifier = trim($data['identifier']);

            if (!$identifier) {
                $identifier = str_replace(' ', '_', trim($data['title']));
            }

            // Make identifier URL friendly
            $identifier = preg_replace('/\W+/', '-', strtolower($identifier));

            // If this identifier already exist in use then append random number to end of identifier
            if (!$this->checkForExistIdentifier($identifier, $data['promotion_id'])) {
                $identifier = $identifier.'_'.rand();
            }

            $data['identifier'] = $identifier;

            $model = $this->promotionFactory->create();

            if ($data['promotion_id']) {
                try {

                    $model = $this->promotionRepository->getById($data['promotion_id']);
                } catch (LocalizedException $e) {

                    $this->messageManager->addErrorMessage(__('This promotion no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {

                $this->promotionRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Promotion.'));

                $this->cacheTypeList->invalidate($this->cacheTypes);
                $this->dataPersistor->clear('kemana_promotion');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['promotion_id' => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {

                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {

                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Promotion.'));
            }

            $this->dataPersistor->set('kemana_promotion', $data);

            return $resultRedirect->setPath('*/*/edit', ['promotion_id' => $this->getRequest()->getParam('promotion_id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function checkForExistIdentifier($identifier, $promotionId = null) {
        try {
            $promotion = $this->promotionHelper->getPromotionIdForIdentifier($identifier, $promotionId);

            if (!empty($promotion)) {
                return false;
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return true;
    }

}
