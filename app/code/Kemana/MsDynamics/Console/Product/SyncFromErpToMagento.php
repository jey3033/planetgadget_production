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
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\MsDynamics\Console\Product;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kemana\MsDynamics\Cron\SyncProductsFromErp;
use Magento\Framework\App\State;

/**
 * Class SyncFromErpToMagento
 */
class SyncFromErpToMagento extends Command
{
    /**
     * @var SyncProductsFromErp
     */
    protected $syncProductsFromErp;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @param string|null $name
     * @param \Magento\Framework\App\State   
     * @param SyncProductsFromErp $syncProductsFromErp
     */
    public function __construct(
        SyncProductsFromErp $syncProductsFromErp,
        State               $state,
        string              $name = null)
    {
        $this->syncProductsFromErp = $syncProductsFromErp;
        $this->state = $state;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('msdynamic:product:get:from:erp:to:magento');
        $this->setDescription('Get un sync products from MyDynamic ERPS and then create product in Magento');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started to get the un sync products from MsDynamic ERP and create new product in Magento");
        $output->writeln("Please check var/log/ms_dynamic.log file for see live messages");
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $fullySyncedProducts = $this->syncProductsFromErp->syncProductsFromErpToMagento();
        $output->writeln("Fully synced products with Ack call to the ERP");
        $output->writeln($fullySyncedProducts);
        $output->writeln("Finished the process. See the logs for more information");

    }
}
