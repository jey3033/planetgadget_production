<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Base
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Base\Helper;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PG_WEBSITEID = 1;
    const PG_STOREID   = 1;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var BlockFactory
     */
    protected $pageFactory;

    /**
     * Data constructor.
     *
     * @param      Context                          $context
     * @param      \Magento\Cms\Model\BlockFactory  $blockFactory  The block factory
     */
    public function __construct(
        Context $context,
        BlockFactory $blockFactory,
        PageFactory $pageFactory
    ) {
        $this->context      = $context;
        $this->blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    /**
     * Creates a block.
     *
     * @param      string  $identifier  The identifier
     * @param      string  $title       The title
     * @param      int     $storeId     The store identifier
     * @param      string  $content     The store label
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
     * update cms block
     *
     * @param      int  $blockId  The block identifier
     * @param      string  $content  The content
     */
    public function updateBlock($blockId, $content)
    {
        if (!empty($blockId) && !empty($content)) {
            $staticBlock = $this->blockFactory->create()->load($blockId, 'block_id');
            $staticBlock->setContent($content);
            $staticBlock->save();
        }
    }

    /**
     * @param $blockId
     * @throws \Exception
     */
    public function deleteBlock($blockId) {
        if (!empty($blockId)) {
            $staticBlock = $this->blockFactory->create()->load($blockId, 'block_id');
            $staticBlock->delete();
        }
    }

    /**
     * @param $pageData
     * @throws \Exception
     */
    public function createPage($pageData)
    {
        if (!empty($pageData)) {
            $this->pageFactory->create()->setData($pageData)->save();
        }
    }

    /**
     * @param $pageId
     * @param $content
     * @throws \Exception
     */
    public function updatePage($pageId, $content)
    {
        if (!empty($pageId) && !empty($content)) {
            $staticPage = $this->pageFactory->create()->load($pageId, 'page_id');
            $staticPage->setContent($content);
            $staticPage->save();
        }
    }

    /**
     * @param $pageId
     * @throws \Exception
     */
    public function deletePage($pageId) {
        if (!empty($pageId)) {
            $staticPage = $this->pageFactory->create()->load($pageId, 'page_id');
            $staticPage->delete();
        }
    }

}
