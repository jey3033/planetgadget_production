<?php
/**
 * Copyright © 2020 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Setup
 * @license  Proprietary
 *
 * @author   Aranga Wijesooria <awijesooriya@kemana.com>, Cipto Raharjo <craharjo@kemana.com>
 */
namespace Kemana\Common\Model\Data;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Category
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Category
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\TreeFactory
     */
    protected $resourceCategoryTreeFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $categoryTree;

    /**
     * @var bool
     */
    protected $mediaInstalled;

    /**
     * @var int
     */
    protected $defaultRootCategoryId;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->fixtureManager              = $sampleDataContext->getFixtureManager();
        $this->csvReader                   = $sampleDataContext->getCsvReader();
        $this->categoryFactory             = $categoryFactory;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->storeManager                = $storeManager;
    }

    /**
     * @param array $fixtures
     * @throws \Exception
     */
    public function install(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            /*if (!file_exists($fileName)) {
            continue;
            }*/
            $filePath = explode('/', $fileName);
            $file     = end($filePath);
            array_pop($filePath);
            $path = implode('/', $filePath);

            $validator = new \Zend_Validate_File_Exists();
            $validator->addDirectory($path);
            if (!$validator->isValid($file)) {
                continue;
            }

            $rows           = $this->csvReader->getData($fileName);
            $header         = array_shift($rows);
            $rootCategories = $this->getRootCategories();
            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }

                $this->defaultRootCategoryId = isset($rootCategories[$data['root_category']]) ? $rootCategories[$data['root_category']] : $this->storeManager->getDefaultStoreView()->getRootCategoryId();
                $this->createCategory($data);
            }
        }
    }

    /**
     * @param array $row
     * @param \Magento\Catalog\Model\Category $category
     * @return void
     */
    protected function setAdditionalData($row, $category)
    {
        $additionalAttributes = [
            'position',
            'display_mode',
            'page_layout',
            'custom_layout_update',
        ];

        foreach ($additionalAttributes as $categoryAttribute) {
            if (!empty($row[$categoryAttribute])) {
                $attributeData = [$categoryAttribute => $row[$categoryAttribute]];
                $category->addData($attributeData);
            }
        }
    }

    /**
     * Get category name by path
     *
     * @param string $path
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getCategoryByPath($path)
    {
        $names    = array_filter(explode('/', $path));
        $rootNode = $this->defaultRootCategoryId;
        $tree     = $this->getTree($rootNode);
        foreach ($names as $name) {
            $tree = $this->findTreeChild($tree, $name);
            if (!$tree) {
                $tree = $this->findTreeChild($this->getTree($rootNode, true), $name);
            }
            if (!$tree) {
                break;
            }
        }
        return $tree;
    }

    /**
     * Get child categories
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @param string $name
     * @return mixed
     */
    protected function findTreeChild($tree, $name)
    {
        $foundChild = null;
        if ($name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $foundChild = $child;
                    break;
                }
            }
        }
        return $foundChild;
    }

    /**
     * Get category tree
     *
     * @param int $rootNode
     * @param bool $reload
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getTree($rootNode, $reload = false)
    {
        if (!isset($this->categoryTree[$rootNode]) || $reload) {
            $tree = $this->resourceCategoryTreeFactory->create();
            $node = $tree->loadNode($rootNode)->loadChildren();
            $tree->addCollectionData(null, false, $rootNode);
            $this->categoryTree[$rootNode] = $node;
        }
        return $this->categoryTree[$rootNode];
    }

    /**
     * @param $row
     * @throws \Exception
     */
    protected function createCategory($row)
    {
        $category = $this->getCategoryByPath($row['path'] . '/' . $row['name']);
        if (!$category) {
            $parentCategory = $this->getCategoryByPath($row['path']);
            if ($parentCategory == null) {
                $f = 0;
            }
            $data           = [
                'parent_id'       => $parentCategory->getId(),
                'name'            => $row['name'],
                'is_active'       => $row['active'],
                'is_anchor'       => $row['is_anchor'],
                'include_in_menu' => $row['include_in_menu'],
                'url_key'         => $row['url_key'],
            ];
            $category = $this->categoryFactory->create();
            $category->setData($data)
                ->setPath($parentCategory->getData('path'))
                ->setAttributeSetId($category->getDefaultAttributeSetId());
            $this->setAdditionalData($row, $category);
            $category->save();
        }
    }

    /**
     * Gets the catalog root categories.
     *
     * @return     array  The root categories.
     */
    protected function getRootCategories()
    {
        $ids            = [];
        $rootCategories = [];
        $stores         = $this->storeManager->getGroups();
        foreach ($stores as $store) {
            $ids[] = $store->getRootCategoryId();
        }

        if (!empty($ids)) {
            foreach ($ids as $categoryId) {
                $category                             = $this->categoryFactory->create()->load($categoryId);
                $rootCategories[$category->getName()] = $categoryId;
            }
        }

        return $rootCategories;
    }
}
