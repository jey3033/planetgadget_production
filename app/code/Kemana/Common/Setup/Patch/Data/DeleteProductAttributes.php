<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Common\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class DeleteProductAttributes
 */
class DeleteProductAttributes implements DataPatchInterface {

    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * EavSetupFactory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory          $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply() {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ($this->attributesToDelete() as $code) {
            $eavSetup->removeAttribute('catalog_product', $code);
        }

    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases() {
        return [];
    }

    /**
     * @return string[]
     */
    public function attributesToDelete(): array
    {
        return [
            'pg_processor',
            'pg_os_detail',
            'pg_display',
            'pg_display_resolution',
            'pg_battery',
            'pg_guarantee',
            'pg_product_condition',
            'pg_launch',
            'hp_ram',
            'hp_rom',
            'hp_os',
            'hp_network',
            'hp_simcard',
            'hp_extmemory',
            'hp_extmemory_limit',
            'hp_camera',
            'hp_selfie',
            'hp_audio',
            'hp_video',
            'hp_dimension',
            'hp_kelengkapan',
            'hp_cpu',
            'hp_gpu',
            'hp_brand',
            'laptop_processor',
            'laptop_memory',
            'laptop_graphical_type',
            'laptop_display_type',
            'laptop_touchscreen',
            'laptop_hdd',
            'laptop_networking',
            'laptop_wireless',
            'laptop_wireless_type',
            'laptop_bluetooth',
            'laptop_interface',
            'laptop_brand',
            'other_dimension'
        ];
    }
}
