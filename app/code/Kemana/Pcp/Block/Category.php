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

namespace Kemana\Pcp\Block;

use Magento\Framework\View\Element\Template;
use Kemana\Pcp\Helper\Data;

/**
 * Class Category
 */
class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Data             $helper,
        Template\Context $context,
        array            $data = []
    )
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryBannerId()
    {
        $pcpBannerId = null;

        $category = $this->helper->loadCategory();
        if ($category) {
            $pcpBannerId = $category->getPcpBannerId();
        }

        return $pcpBannerId;
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadSubCategories()
    {
        $children = null;

        $category = $this->helper->loadCategory();
        if ($category) {
            $children = $category->getChildren();
        }

        return $children;
    }

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareSubCategoryData()
    {
        $subCategoryData = [];
        $length = $this->helper->getSubCategoryLengthInPcp();
        $subCategories = $this->loadSubCategories();

        if ($subCategories) {
            $subCategories = explode(',', $subCategories);

            if (is_array($subCategories)) {

                $loopLength = 1;
                foreach ($subCategories as $subCategory) {
                    if ($loopLength > $length) {
                        break;
                    }

                    $loadCategory = $this->helper->loadCategory($subCategory);

                    $subCategoryData[] = [
                        "name" => $loadCategory->getName(),
                        "shortDescription" => $loadCategory->getShortDescription(),
                        "image" => $loadCategory->getImageUrl()
                    ];

                    $length++;
                }
            }

            return $subCategoryData;
        }

        return null;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFeaturedGadgetSkus()
    {
        $skus = [];

        $category = $this->helper->loadCategory();
        if ($category) {
            $skus = $category->getPcpFeatureProductSkus();
        }

        return $skus;
    }
}
