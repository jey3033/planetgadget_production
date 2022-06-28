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

use Kemana\Common\Helper\Data as HelperData;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CreateHomeCmsBlocksBlogAndNews
 */
class CreateHomeCmsBlocksBlogAndNews implements DataPatchInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param BlockFactory $blockFactory
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     */
    public function __construct(
        BlockFactory          $blockFactory,
        StoreManagerInterface $storeManager,
        HelperData            $helperData
    )
    {
        $this->blockFactory = $blockFactory;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
    }

    /**
     * @return CreateHomeCmsBlocks|void
     */
    public function apply()
    {
        $blocksData = $this->getBlockData();

        foreach ($blocksData as $identifier => $block) {

            $getBlock = $this->blockFactory->create()
                ->getCollection()
                ->addFieldToFilter('identifier', $identifier)
                ->addFieldToFilter('store_id')
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
            "home_page_blog_and_news" => ['title' => 'Blog & News Home Page',
                'content' => '{{widget type="Kemana\Blog\Block\Widget\Posts" post_count="3" show_type="new" template="Kemana_Blog::widget/posts.phtml"}}',
                'is_active' => 1,
                'store_id' => [HelperData::PG_STORE_VIEW_INDONESIA, HelperData::PG_STORE_VIEW_ENGLISH]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
