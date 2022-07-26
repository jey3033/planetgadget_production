<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_CustomerMembership
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\CustomerMembership\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\GroupFactory;

/**
 * Class AddCustomerGroup
 */
class AddCustomerGroup implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var GroupFactory
     */
    private $customerGroupFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param GroupFactory $customerGroupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        GroupFactory             $customerGroupFactory
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerGroupFactory = $customerGroupFactory;
    }

    /**
     * @return CustomerMembership|void
     */
    public function apply()
    {
        foreach ($this->GetCustomerGroupsForMembership() as $group) {
            $groupFactory = $this->customerGroupFactory->create();

            $groupFactory->setCode($group)
                ->setTaxClassId(3)
                ->save();
        }
    }

    /**
     * @return string[]
     */
    public function GetCustomerGroupsForMembership(): array
    {
        return ['Gold', 'Platinum'];
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
