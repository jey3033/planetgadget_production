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
use Magento\Framework\App\State;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product;

/**
 * Class SyncFromErpToMagento
 */
class UpdateProductWeight extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @param string|null $name
     * @param \Magento\Framework\App\State   
     */
    public function __construct(
        State               $state,
        CollectionFactory   $collectionFactory,
        Product             $product,
        string              $name = null)
    {
        $this->state = $state;
        $this->collectionFactory = $collectionFactory;
        $this->product = $product;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('product:weight:update');
        $this->setDescription('if product weight 0 then update product weight 0.5');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started to update product weight");
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $collections = $this->collectionFactory->create();
        foreach ($collections as $product) {
            try {
                if($product->getTypeId() != "configurable"){
                    $id = $product->getId(); 
                    $product = $this->product->load($id);
                    if($product->getWeight() > 0){
                        $output->writeln($id." already product weight is greater than 0.");        
                    }else{
                        $product->setWeight(0.5);
                        $product->save();
                        $output->writeln("updated product id: ". $id ." and weight: 0.5.");
                    }
                }else{
                    $output->writeln("product type is configure.");
                }
            } catch (Exception $e) {
                $output->writeln($e->getMessage());                
            }
        }
        $output->writeln("Weight Updated for all products.");
    }
}
