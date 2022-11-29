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
namespace Kemana\MsDynamics\Controller\Adminhtml\Order;

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
     * @var \Kemana\MsDynamics\Cron\SyncOrdersToErp
     */
    protected $syncOrdersToErp;

    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param \Kemana\MsDynamics\Cron\SyncCustomersToErp $syncCustomersToErp
     * @param \Kemana\MsDynamics\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Kemana\MsDynamics\Cron\SyncOrdersToErp $syncOrdersToErp,
        \Kemana\MsDynamics\Helper\Data                    $helper
    )
    {
        $this->syncOrdersToErp = $syncOrdersToErp;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->helper = $helper;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute()
    {
        $this->helper->log('Start to send customer from click event in Customer admin grid.', 'info');

        $orderResult = false;
        $pushOrder = false;

        if ($this->getRequest()->getPost('orderId') &&
            $this->getRequest()->getPost('callingFrom') == 'orderGrid') {

            $orderId = $this->getRequest()->getPost('orderId');

            $pushOrder = $this->syncOrdersToErp->syncOrdersFromMagentoToErp(0, $orderId);
        }

        if ($pushOrder['result']) {
            $orderResult = true;
            $this->helper->log('End send order from click event in Admin Order Grid.', 'info');
        }

        return $this->getResponse()->representJson(
            $this->jsonSerializer->serialize(['result' => $pushOrder])
        );
    }

}
