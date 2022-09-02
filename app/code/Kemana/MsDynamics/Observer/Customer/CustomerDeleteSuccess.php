<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamic
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\MsDynamics\Observer\Customer;

use Magento\Framework\Event\Observer;

/**
 * Class CustomerDeleteSuccess
 */
class CustomerDeleteSuccess implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Kemana\MsDynamics\Helper\Data
     */
    protected $helper;

    /**
     * @var \Kemana\MsDynamics\Model\Api\Erp\Customer
     */
    protected $erpCustomer;

    /**
     * @param \Kemana\MsDynamics\Helper\Data $helper
     * @param \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
     */
    public function __construct(
        \Kemana\MsDynamics\Helper\Data            $helper,
        \Kemana\MsDynamics\Model\Api\Erp\Customer $erpCustomer
    )
    {
        $this->helper = $helper;
        $this->erpCustomer = $erpCustomer;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->helper->log('Started the Customer Account Delete Event.', 'info');

        $customer = $observer->getCustomer();

        if ($customer->getMsDynamicCustomerNumber()) {
            $customerId = $customer->getId();
            $erpCustomerNumber = $customer->getMsDynamicCustomerNumber();

            $this->helper->log('Started to delete the customer' . $erpCustomerNumber . ' from ERP', 'info');

            $dataToDeleteCustomer = [
                "customer_no" => $erpCustomerNumber
            ];

            $dataToDeleteCustomer = $this->helper->convertArrayToXml($dataToDeleteCustomer);

            $deleteCustomerInErp = $this->erpCustomer->deleteCustomerInErp($this->helper->getFunctionDeleteCustomer(),
                $this->helper->getSoapActionDeleteCustomer(), $dataToDeleteCustomer);

            if (isset($deleteCustomerInErp['curlStatus']) == '200') {

                $this->helper->log('Customer ERP Number ' . $erpCustomerNumber . ' deleted successfully in ERP. Magento ID ' . $customerId, 'info');
                $this->helper->log('Finished the Customer Account Delete Event', 'info');
            }
        }
    }
}
