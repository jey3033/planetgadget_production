<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_CmsBlock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Tushar Korat <tushar@kemana.com>
 */

namespace Kemana\CmsBlock\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Kemana\CmsBlock\Helper\Data as HelperData;

/**
 * Class AddNewFooterCmsStaticBlock
 * @package Kemana\CmsBlock\Setup\Patch\Data
 */
class AddNewFooterCmsStaticBlock implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * AddAccessViolationPageAndAssignB2CCustomers constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageFactory $blockFactory
     * @param HelperData $helperData
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory,
        HelperData $helperData
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
        $this->helperData = $helperData;
    }

    /**
     * Apply Block
     *
     * {@inheritdoc}
     */
    public function apply()
    {
        $blocksData = $this->getBlockData();
        foreach ($blocksData as $identifier => $block) {

            $getBlock = $this->blockFactory->create()
                ->getCollection()
                ->addFieldToFilter('identifier', $identifier)
                ->addFieldToFilter('store_id', \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ->getLastItem();

            if (empty($getBlock->getData())) {
                $this->helperData->createBlock($identifier, $block['title'], $block['store_id'], $block['content']);
            } else {
                $this->helperData->updateBlock($getBlock->getData('block_id'), $block['store_id'], $block['content']);
            }
        }
    }

    /**
     * Get CMS Block Data
     * @return array[]
     */
    public function getBlockData(): array
    {
        return [
            "footer-privacy-policy" => ['title' => 'Footer Privacy Policy',
                'content' => '<ul class="list" role="tabpanel" aria-hidden="true" style="display: none;">
                    <li class="nav item"><a href="{{store url="catalogsearch/advanced/"}}" data-action="advanced-search">Advanced Search</a></li>
                    <li class="nav item"><a href="{{store url="sales/guest/form/"}}">Orders and Returns</a></li><li class="nav item"><a href="{{store url="contact/"}}">Kontak Kami</a></li>
                    <li class="nav item"><a href="{{store url="search/term/popular/"}}">Search Terms</a></li>
                    </ul>',
                'is_active' => 1,
                'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ]
        ];
    }

    /**
     * Get Dependencies
     *
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get Version
     *
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * Get Aliases
     *
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}