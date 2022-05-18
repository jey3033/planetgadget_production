<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Pcp
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Pcp\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Setup\Exception;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Request\Http;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Get how many sub categories are show on the category landing page
     */
    const XML_PATH_SUB_CATEGORY_LENGTH_IN_PCP = 'catalog/category_landing_page/pcp_sub_category_length';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * @var string
     */
    protected $storeScope;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CategoryRepository $categoryRepository
     * @param Http $httpRequest
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryRepository    $categoryRepository,
        Http                  $httpRequest,
        Context               $context
    )
    {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->httpRequest = $httpRequest;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        parent::__construct($context);
    }

    /**
     * @param $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadCategory($categoryId = null)
    {

        if (!$categoryId) {

            if (!$this->getCategoryId()) {
                return null;
            }

            $categoryId = $this->getCategoryId();
        }

        return $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
    }

    /**
     * @return mixed|null
     */
    public function getCategoryId()
    {
        if ($this->httpRequest->getParam('id')) {
            return $this->httpRequest->getParam('id');
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getSubCategoryLengthInPcp()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SUB_CATEGORY_LENGTH_IN_PCP, $this->storeScope);
    }

}
