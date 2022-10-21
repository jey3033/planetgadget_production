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

namespace Kemana\CmsBlock\Helper;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Reports\Model\ResourceModel\Product\Sold\CollectionFactory;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @param Context $context
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        Context $context,
        BlockFactory $blockFactory
    ) {
        $this->context      = $context;
        $this->blockFactory = $blockFactory;

        parent::__construct($context);
    }

    /**
     * @param $identifier
     * @param $title
     * @param $storeId
     * @param $content
     * @return void
     * @throws \Exception
     */
    public function createBlock($identifier, $title, $storeId, $content)
    {
        if (!empty($content)) {
            $staticBlock = $this->blockFactory->create();
            $staticBlock->setIdentifier($identifier)
                ->setTitle($title)
                ->setIsActive(true)
                ->setStoreId($storeId)
                ->setContent($content);

            $staticBlock->save();
        }
    }

    /**
     * @param $blockId
     * @param $storeId
     * @param $content
     * @return void
     * @throws \Exception
     */
    public function updateBlock($blockId, $storeId, $content)
    {
        if (!empty($blockId) && !empty($content)) {
            $staticBlock = $this->blockFactory->create()->load($blockId, 'block_id');
            $staticBlock->setContent($content)
                ->setStoreId($storeId);
            $staticBlock->save();
        }
    }
}
