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
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;

/**
 * Class CreateStaticCmsPages
 * @package Kemana\Setup\Setup\Patch\Data
 */
class UpdateCmsStaticPages implements DataPatchInterface
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
        $cmsPageData = $this->getOldStaticCmsPageData();

        foreach ($cmsPageData as $pageData) {

            $filterIdentifier = $this->filterBuilder
                ->setField('identifier')
                ->setConditionType('eq')
                ->setValue($pageData['identifier'])
                ->create();

            $filterStore = $this->filterBuilder
                ->setField('store_id')
                ->setConditionType('eq')
                ->setValue($pageData['store_id'])
                ->create();

            $this->searchCriteriaBuilder->addFilters([$filterIdentifier, $filterStore]);
            $this->searchCriteriaBuilder->setPageSize(1);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $page = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();

            if (!empty($page)) {
                $pageId = array_key_first($page);
                $this->helperData->deletePage($page[$pageId]->getData('page_id'));
            }
        }

        $cmsNewPageData = $this->getNewStaticCmsPageData();
        foreach ($cmsNewPageData as $pageData) {
            $this->helperData->createPage($pageData);
        }
    }

    /**
     * @return array[]
     */
    public function getOldStaticCmsPageData(): array
    {
        return [
            [
                'title' => 'Contact Us',
                'page_layout' => '1column',
                'identifier' => 'contact-us',
                'content_heading' => 'Contact Us',
                'content' => '<p>Contact Us</p>',
                'url_key' => 'contact-us',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'About Us',
                'page_layout' => '1column',
                'identifier' => 'about-us',
                'content_heading' => 'About Us',
                'content' => '<p>About Us</p>',
                'url_key' => 'about-us',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Term and Conditions',
                'page_layout' => '1column',
                'identifier' => 'terms-and-conditions',
                'content_heading' => 'Term and Conditions',
                'content' => '<p>Term and Conditions</p>',
                'url_key' => 'terms-and-conditions',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Frequently Asked Questions',
                'page_layout' => '1column',
                'identifier' => 'frequently-asked-questions',
                'content_heading' => 'Frequently Asked Questions',
                'content' => '<p>Frequently Asked Questions</p>',
                'url_key' => 'frequently-asked-questions',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Privacy Policy',
                'page_layout' => '1column',
                'identifier' => 'privacy-policy',
                'content_heading' => 'Privacy Policy',
                'content' => '<p>Privacy Policy</p>',
                'url_key' => 'privacy-policy',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'How To Order',
                'page_layout' => '1column',
                'identifier' => 'how-to-order',
                'content_heading' => 'How To Order',
                'content' => '<p>How To Order</p>',
                'url_key' => 'how-to-order',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<p>How To Pay</p>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Shipping Information',
                'page_layout' => '1column',
                'identifier' => 'shipping-information',
                'content_heading' => 'Shipping Information',
                'content' => '<p>Shipping Information</p>',
                'url_key' => 'shipping-information',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Warranty Policy',
                'page_layout' => '1column',
                'identifier' => 'warranty-policy',
                'content_heading' => 'Warranty Policy',
                'content' => '<p>Warranty Policy</p>',
                'url_key' => 'warranty-policy',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Trade-In Plus',
                'page_layout' => '1column',
                'identifier' => 'trade-in-plus',
                'content_heading' => 'Trade-In Plus',
                'content' => '<p>Trade-In Plus</p>',
                'url_key' => 'trade-in-plus',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Career',
                'page_layout' => '1column',
                'identifier' => 'career',
                'content_heading' => 'Career',
                'content' => '<p>Career</p>',
                'url_key' => 'career',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Contact Us',
                'page_layout' => '1column',
                'identifier' => 'contact-us',
                'content_heading' => 'Contact Us',
                'content' => '<p>Contact Us</p>',
                'url_key' => 'contact-us',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'About Us',
                'page_layout' => '1column',
                'identifier' => 'about-us',
                'content_heading' => 'About Us',
                'content' => '<p>About Us</p>',
                'url_key' => 'about-us',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Term and Conditions',
                'page_layout' => '1column',
                'identifier' => 'terms-and-conditions',
                'content_heading' => 'Term and Conditions',
                'content' => '<p>Term and Conditions</p>',
                'url_key' => 'terms-and-conditions',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Frequently Asked Questions',
                'page_layout' => '1column',
                'identifier' => 'frequently-asked-questions',
                'content_heading' => 'Frequently Asked Questions',
                'content' => '<p>Frequently Asked Questions</p>',
                'url_key' => 'frequently-asked-questions',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Privacy Policy',
                'page_layout' => '1column',
                'identifier' => 'privacy-policy',
                'content_heading' => 'Privacy Policy',
                'content' => '<p>Privacy Policy</p>',
                'url_key' => 'privacy-policy',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'How To Order',
                'page_layout' => '1column',
                'identifier' => 'how-to-order',
                'content_heading' => 'How To Order',
                'content' => '<p>How To Order</p>',
                'url_key' => 'how-to-order',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<p>How To Pay</p>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Shipping Information',
                'page_layout' => '1column',
                'identifier' => 'shipping-information',
                'content_heading' => 'Shipping Information',
                'content' => '<p>Shipping Information</p>',
                'url_key' => 'shipping-information',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Warranty Policy',
                'page_layout' => '1column',
                'identifier' => 'warranty-policy',
                'content_heading' => 'Warranty Policy',
                'content' => '<p>Warranty Policy</p>',
                'url_key' => 'warranty-policy',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Trade-In Plus',
                'page_layout' => '1column',
                'identifier' => 'trade-in-plus',
                'content_heading' => 'Trade-In Plus',
                'content' => '<p>Trade-In Plus</p>',
                'url_key' => 'trade-in-plus',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Career',
                'page_layout' => '1column',
                'identifier' => 'career',
                'content_heading' => 'Career',
                'content' => '<p>Career</p>',
                'url_key' => 'career',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function getNewStaticCmsPageData(): array
    {
        return [
            [
                'title' => 'Contact Us',
                'page_layout' => '1column',
                'identifier' => 'contact-us',
                'content_heading' => 'Contact Us',
                'content' => '<p>Contact Us</p>',
                'url_key' => 'contact-us',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'About Us',
                'page_layout' => '1column',
                'identifier' => 'about-us',
                'content_heading' => 'About Us',
                'content' => '<p>About Us</p>',
                'url_key' => 'about-us',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Term and Conditions',
                'page_layout' => '1column',
                'identifier' => 'terms-and-conditions',
                'content_heading' => 'Term and Conditions',
                'content' => '<p>Term and Conditions</p>',
                'url_key' => 'terms-and-conditions',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Frequently Asked Questions',
                'page_layout' => '1column',
                'identifier' => 'frequently-asked-questions',
                'content_heading' => 'Frequently Asked Questions',
                'content' => '<p>Frequently Asked Questions</p>',
                'url_key' => 'frequently-asked-questions',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Privacy Policy',
                'page_layout' => '1column',
                'identifier' => 'privacy-policy',
                'content_heading' => 'Privacy Policy',
                'content' => '<p>Privacy Policy</p>',
                'url_key' => 'privacy-policy',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'How To Order',
                'page_layout' => '1column',
                'identifier' => 'how-to-order',
                'content_heading' => 'How To Order',
                'content' => '<p>How To Order</p>',
                'url_key' => 'how-to-order',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<p>How To Pay</p>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Shipping Information',
                'page_layout' => '1column',
                'identifier' => 'shipping-information',
                'content_heading' => 'Shipping Information',
                'content' => '<p>Shipping Information</p>',
                'url_key' => 'shipping-information',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Warranty Policy',
                'page_layout' => '1column',
                'identifier' => 'warranty-policy',
                'content_heading' => 'Warranty Policy',
                'content' => '<p>Warranty Policy</p>',
                'url_key' => 'warranty-policy',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Trade-In Plus',
                'page_layout' => '1column',
                'identifier' => 'trade-in-plus',
                'content_heading' => 'Trade-In Plus',
                'content' => '<p>Trade-In Plus</p>',
                'url_key' => 'trade-in-plus',
                'is_active' => 1,
                'store_id' => 0
            ],
            [
                'title' => 'Career',
                'page_layout' => '1column',
                'identifier' => 'career',
                'content_heading' => 'Career',
                'content' => '<p>Career</p>',
                'url_key' => 'career',
                'is_active' => 1,
                'store_id' => 0
            ]
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
