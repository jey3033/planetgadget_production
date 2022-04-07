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

use Kemana\Common\Model\Data\Category;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateCategories
 */
class CreateCategories implements DataPatchInterface
{

    const CATEGORY_FIXTURE = ['Kemana_Common::fixtures/categories.csv'];
    /**
     * @var Category
     */
    private $category;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var State
     */
    private $appState;

    /**
     * CreateCategories constructor.
     * @param Category $category
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        Category $category,
        ModuleDataSetupInterface $moduleDataSetup,
        State $appState
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->category        = $category;
        $this->appState        = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        //$this->appState->setAreaCode(Area::AREA_FRONTEND);
        try {
            $this->category->install(self::CATEGORY_FIXTURE);
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
