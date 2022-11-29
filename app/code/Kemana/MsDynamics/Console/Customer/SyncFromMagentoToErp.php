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

namespace Kemana\MsDynamics\Console\Customer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kemana\MsDynamics\Cron\SyncCustomersToErp;

/**
 * Class SyncFromErpToMagento
 */
class SyncFromMagentoToErp extends Command
{
    /**
     * @var SyncCustomersFromErp
     */
    protected $syncCustomersToErp;

    /**
     * @param SyncCustomersToErp $syncCustomersToErp
     * @param string|null $name
     */
    public function __construct(
        SyncCustomersToErp $syncCustomersToErp,
        string             $name = null)
    {
        $this->syncCustomersToErp = $syncCustomersToErp;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('msdynamic:customer:send:from:magento:to:erp');
        $this->setDescription('Get the customers from Magento who does not have MsDynamicErpCustomerNumber and then
        push to the ERP and update the MsDynamicErpCustomerNumber in Magento');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started to get the customers from Magento who does not have MsDynamicErpCustomerNumber and then
        push to the ERP and update the MsDynamicErpCustomerNumber in Magento");
        $output->writeln("Please check var/log/ms_dynamic.log file for see live messages");
        $this->syncCustomersToErp->syncMissingCustomersFromRealTimeSync(0);
        $output->writeln("Finished the process. See the logs for more information");

    }
}
