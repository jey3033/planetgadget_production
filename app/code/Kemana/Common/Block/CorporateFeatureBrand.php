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
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

namespace Kemana\Common\Block;

use Kemana\Brand\Block\AbstractBrand;
use Kemana\Brand\Helper\Data as BrandHelper;
use Kemana\Brand\Model\ItemsFactory as BrandItemCollection;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class CorporateFeatureBrand
 */
class CorporateFeatureBrand extends AbstractBrand
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Brand constructor.
     * @param Template\Context $context
     * @param Json $json
     * @param BrandHelper $brandHelper
     * @param BrandItemCollection $brandItemCollection
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Json $json,
        BrandHelper $brandHelper,
        BrandItemCollection $brandItemCollection,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $json, $brandHelper, $brandItemCollection, $categoryRepository, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @return int
     */
    public function isBrandEnable()
    {
        return $this->getBrandHelper()->isModuleEnabled();
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFeaturedBrandCollection()
    {
        $featuredBrands = [];
        $storeId = $this->storeManager->getStore()->getId();
        $featuredBrands = $this->getBrandCollection()->create()->getCollection()->getData();

        return $featuredBrands;
    }

    /**
     * @return string
     */
    public function getProductLimit()
    {
        return $this->getBrandHelper()->getItemSizeFeatured();
    }

    /**
     * @return bool
     */
    public function isShowFeatured()
    {
        return $this->getBrandHelper()->isShowFeatured();
    }

    /**
     * @return mixed
     */
    public function getFeaturedBrandTitle()
    {
        return $this->getBrandHelper()->getFeaturedBrandTitle();
    }
}
