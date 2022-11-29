<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */
namespace Kemana\MsDynamics\Controller\Adminhtml\Customer;

/**
 * Class Index
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonSerializer;

    /**
     * @var \Kemana\MsDynamics\Cron\SyncCustomersToErp
     */
    protected $syncCustomersToErp;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Cron\SyncRewardPointToErp
     */
    protected $syncRewardPointToErp;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Kemana\MsDynamics\Cron\SyncCustomersToErp $syncCustomersToErp
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Cron\SyncRewardPointToErp $syncRewardPointToErp
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Kemana\MsDynamics\Cron\SyncCustomersToErp $syncCustomersToErp,
        \Kemana\MsDynamics\Helper\Data                    $helper,
        \Kemana\MsDynamics\Cron\SyncRewardPointToErp $syncRewardPointToErp
    )
    {
        $this->syncCustomersToErp = $syncCustomersToErp;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->helper = $helper;
        $this->syncRewardPointToErp = $syncRewardPointToErp;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute()
    {
        $this->helper->log('Start to send customer from click event in Customer admin grid.', 'info');

        $customerResult = false;
        $pushCustomer = false;

        if ($this->getRequest()->getPost('customerId') &&
            $this->getRequest()->getPost('callingFrom') == 'customerGrid') {

            $customerId = $this->getRequest()->getPost('customerId');

            $pushCustomer = $this->syncCustomersToErp->syncMissingCustomersFromRealTimeSync(0, $customerId);
            $this->syncRewardPointToErp->syncRewardPointFromMagentoToErp(0, $customerId);
        }

        if ($pushCustomer['result']) {
            $customerResult = true;
            $this->helper->log('End send customer from click event in Customer admin grid.', 'info');
        }

        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize(['result' => $pushCustomer])
        );
    }

}
