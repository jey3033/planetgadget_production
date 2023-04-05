<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Kemana\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Kemana\Banner\Model\ResourceModel\Banner;

/**
 * Class MassStatus
 * @package Kemana\Banner\Controller\Adminhtml\Banner
 */
class MassStatus extends Action
{
    /**
     * Mass Action Filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * Collection Factory
     *
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var Banner
     */
    protected $banner;

    /**
     * MassStatus constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Banner $banner
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Banner $banner
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->banner = $banner;
        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $status = (int)$this->getRequest()->getParam('status');

        $ids = $collection->getAllIds();

        try {
            $update = $this->banner->updateStatus($ids, ['status'=>$status]);
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while updating status.'));
        }

        if ($update) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $update));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
