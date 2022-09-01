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
use Kemana\MsDynamics\Cron\SyncCustomersFromErp;

/**
 * Class SyncFromErpToMagento
 */
class SyncFromErpToMagento extends Command
{
    /**
     * @var SyncCustomersFromErp
     */
    protected $syncCustomersFromErp;

    /**
     * @param string|null $name
     * @param SyncCustomersFromErp $syncCustomersFromErp
     */
    public function __construct(
        SyncCustomersFromErp $syncCustomersFromErp,
        string               $name = null)
    {
        $this->syncCustomersFromErp = $syncCustomersFromErp;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('msdynamic:customer:get:from:erp:to:magento');
        $this->setDescription('Get un sync customers from MyDynamic ERPS and then create accounts in Magento');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started to get the un sync customers from MsDynamic ERP and create accounts in Magento");
        $output->writeln("Please check var/log/ms_dynamic.log file for see live messages");
        $fullySyncedCustomers = $this->syncCustomersFromErp->syncCustomersFromErpToMagento();
        $output->writeln("Fully synced customers with Ack call to the ERP");
        $output->writeln($fullySyncedCustomers);
        $output->writeln("Finished the process. See the logs for more information");

    }
}
