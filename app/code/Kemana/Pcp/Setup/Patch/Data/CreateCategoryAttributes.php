<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Pcp
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Pcp\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Category;

/**
 * Class CreateCategoryAttributes
 * @package Kemana\Setup\Setup\Patch\Data
 */
class CreateCategoryAttributes implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory          $eavSetupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return CreateCategoryAttributes|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ($this->getCategoryAttributes() as $id => $data) {
            $eavSetup->removeAttribute(Category::ENTITY, $id);
            $eavSetup->addAttribute(Category::ENTITY, $id, $data);
        }

    }

    /**
     * @return array[]
     */
    public function getCategoryAttributes()
    {
        return [
            'pcp_banner_id' => [
                'type' => 'text',
                'label' => 'Category Page Banner ID',
                'input' => 'text',
                'sort_order' => 46,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Content',
                'visible_on_front' => true
            ],
            "pcp_feature_product_skus" => [
                'type' => 'text',
                'label' => 'Category Page Feature Product SKUs',
                'input' => 'text',
                'sort_order' => 48,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Content',
                'visible_on_front' => true
            ]
        ];
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }

}

