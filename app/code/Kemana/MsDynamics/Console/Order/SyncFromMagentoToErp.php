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

namespace Kemana\MsDynamics\Console\Order;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kemana\MsDynamics\Cron\SyncOrdersToErp;

/**
 * Class SyncFromErpToMagento
 */
class SyncFromMagentoToErp extends Command
{
    /**
     * @var SyncOrdersToErp
     */
    protected $syncOrdersToErp;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @param SyncOrdersToErp $syncOrdersToErp
     * @param \Magento\Framework\App\State $appState
     * @param string|null $name
     */
    public function __construct(
        SyncOrdersToErp $syncOrdersToErp,
        \Magento\Framework\App\State $appState,
        string               $name = null)
    {
        $this->syncOrdersToErp = $syncOrdersToErp;
        $this->appState = $appState;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('msdynamic:order:push:from:magento:to:erp');
        $this->setDescription('Get un sync orders from Magento and then push or ERP');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $output->writeln("Started to push the not sync orders from Magento to ERP");
        $output->writeln("Please check var/log/ms_dynamic.log file for see live messages");
        $this->syncOrdersToErp->syncOrdersFromMagentoToErp(0);
        $output->writeln("Finished the process. See the logs for more information");
    }
}
