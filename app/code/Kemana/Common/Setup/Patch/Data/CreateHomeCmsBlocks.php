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
 * Class CreateHomeCmsBlocks
 */
class CreateHomeCmsBlocks implements DataPatchInterface
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
        //$storeID = HelperData::PG_STORE_ID;
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
            "recommended_from_seller_home_page" => ['title' => 'Recommended From Seller Home Page',
                'content' => '<style>#html-body [data-pb-style=QXFKSQV]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}</style>
<div data-content-type="row" data-appearance="contained" data-element="main">
<div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="QXFKSQV"><div data-content-type="products" data-appearance="carousel" data-autoplay="false" data-autoplay-speed="4000" data-infinite-loop="false" data-show-arrows="false" data-show-dots="true" data-carousel-mode="default" data-center-padding="90px" data-element="main">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" template="Magento_PageBuilder::catalog/product/widget/content/carousel.phtml" anchor_text="" id_path="" show_pager="0" products_count="20" condition_option="sku" condition_option_value="Sample,I_Phone" type_name="Catalog Products Carousel" conditions_encoded="^[`1`:^[`aggregator`:`all`,`new_child`:``,`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`value`:`1`^],`1--1`:^[`operator`:`()`,`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`sku`,`value`:`Sample,I_Phone`^]^]" sort_order="position_by_sku"}}</div></div></div>',
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
