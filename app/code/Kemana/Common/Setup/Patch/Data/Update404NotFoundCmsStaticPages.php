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
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Common\Setup\Patch\Data;

use Kemana\Common\Helper\Data as HelperData;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class Update404NotFoundCmsStaticPages
 */
class Update404NotFoundCmsStaticPages implements DataPatchInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepositoryInterface;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     * @param PageRepositoryInterface $pageRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        PageFactory             $pageFactory,
        StoreManagerInterface   $storeManager,
        HelperData              $helperData,
        PageRepositoryInterface $pageRepositoryInterface,
        SearchCriteriaBuilder   $searchCriteriaBuilder,
        FilterBuilder           $filterBuilder
    )
    {
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return CreateCmsStaticPages|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $filterIdentifier = $this->filterBuilder
            ->setField('identifier')
            ->setConditionType('eq')
            ->setValue('no-route')
            ->create();

        $filterStore = $this->filterBuilder
            ->setField('store_id')
            ->setConditionType('eq')
            ->setValue('no-route')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filterIdentifier, $filterStore]);
        $this->searchCriteriaBuilder->setPageSize(1);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $page = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();

        if (!empty($page)) {
            $pageId = array_key_first($page);
            $this->helperData->deletePage($page[$pageId]->getData('page_id'));
        }
        $cmsNewPageData = $this->getNewStaticCmsPageData();
        $this->helperData->createPage($cmsNewPageData);
    }


    /**
     * @return array[]
     */
    public function getNewStaticCmsPageData(): array
    {
        return [
            'title' => '404 Not Found',
            'page_layout' => '1column',
            'identifier' => 'no-route',
            'content_heading' => '',
            'content' => '<div class="no-route-page-wrapper">
                <div class="no-route-page">
                    <div class="image-div"></div>
                    <h2 class="line-one">{{trans "Oops, Page Not Found"}}</h2>
                    <p class="line-two">{{trans "You can use the search field above, or return to the Homepage"}}</p>
                    <div class="actions-toolbar">
                        <div class="primary">
                            <a class="action primary continue" href="{{store url=""}}"><span>{{trans "Back to Homepage"}}</span></a>
                        </div>
                    </div>
                </div>
            </div>',
            'url_key' => 'no-route',
            'is_active' => 1,
            'store_id' => 0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
