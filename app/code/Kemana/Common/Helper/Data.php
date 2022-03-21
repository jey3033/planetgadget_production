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

namespace Kemana\Common\Helper;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * PG store ID
     */
    const PG_STORE_ID = 1;

    /**
     * Indonesia store view ID
     */
    const PG_STORE_VIEW_INDONESIA = 1;

    /**
     * English store view ID
     */
    const PG_STORE_VIEW_ENGLISH   = 2;

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
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param BlockFactory $blockFactory
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        BlockFactory $blockFactory,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->context      = $context;
        $this->blockFactory = $blockFactory;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;

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

    /**
     * @param $blockId
     * @return void
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
     * @return void
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
     * @return void
     * @throws \Exception
     */
    public function deletePage($pageId) {
        if (!empty($pageId)) {
            $staticPage = $this->pageFactory->create()->load($pageId, 'page_id');
            $staticPage->delete();
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaFolderUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

}
