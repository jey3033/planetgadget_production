<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Common
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Common\Setup\Patch\Data;

use Kemana\Common\Model\Data\Attribute;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddWarrantyProductAttribute
 * @package Kemana\Common\Setup\Patch\Data
 */
class AddWarrantyProductAttribute implements DataPatchInterface
{
    /**
     * Attribute data CSV file path
     */
    const ATTRIBUTE_FIXTURE = ['Kemana_Common::fixtures/warranty_product_attribute.csv'];

    /**
     * @var Attribute
     */
    private $attribute;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param Attribute $attribute
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param State $appState
     */
    public function __construct(
        Attribute $attribute,
        ModuleDataSetupInterface $moduleDataSetup,
        State $appState
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->attribute       = $attribute;
        $this->appState        = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        try {
            $this->attribute->install(self::ATTRIBUTE_FIXTURE);
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    /**
     *  Uninstall or remove all the data when related to this module
     */
    public function revert()
    {

    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
