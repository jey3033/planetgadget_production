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
 * Class CreatePgClubCmsStaticPages
 */
class CreatePgClubCmsStaticPages implements DataPatchInterface
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
        $cmsPageData = $this->getStaticCmsPageData();

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

            if (empty($page)) {
                $this->helperData->createPage($pageData);
            } else {
                $pageId = array_key_first($page);
                $this->helperData->updatePage($page[$pageId]->getData('page_id'), $pageData['content']);
            }
        }
    }

    /**
     * @return array[]
     */
    public function getStaticCmsPageData()
    {
        $pg_club_content = '<div class="pg-club-banner"></div>
        <div class="pg-club-membership cms-content">
            <div class="pg-club-membership-info">
                <h2>Join Member, Nikmati Keuntungannya!</h2>
                <p>Mau dapetin banyak benefit saat berbelanja di Planet Gadget? Planet Gadget Club hadir untuk kamu yang ingin merasakan pengalaman shopping dan service terbaik dari kami! Caranya Mudah banget. Cukup daftarkan akun Anda di PlanetGadget.store, Anda akan mendapatkan poin gratis, serta pelayanan prioritas serta berbagai benefit dari Planet Gadget..</a></p>
            </div>
            <div class="pg-club-membership-benefits">
                <h3>Keuntungan Menjadi Member</h3>
                <div class="row">
                    <div class="col-md-6 pg-gold-member-block">
                        <h4>Gold Member</h4>
                        <ul clas="gold-membership-ul-wrapper">
                            <li>Raih poin setiap pembelanjaan</li>
                            <li>Diskon jasa service di Planet Gadget terdekat</li>
                            <li>Gratis parkir di Planet Gadget Store terdekat</li>
                            <li>Diskon Merchant Partner</li>
                            <li>Dapatkan penawaran produk terbaru</li>
                            <li>Diskon untuk pembelian aksesoris</li>
                            <li>Tukar poin member</li>
                        </ul>
                    </div>
                    <div class="col-md-6 pg-premium-member-block">
                        <h4>Premium Member</h4>
                        <ul clas="gold-membership-ul-wrapper">
                            <li>Raih poin setiap pembelanjaan</li>
                            <li>Diskon jasa service di Planet Gadget terdekat</li>
                            <li>Gratis parkir di Planet Gadget Store terdekat</li>
                            <li>Diskon Merchant Partner</li>
                            <li>Dapatkan penawaran produk terbaru</li>
                            <li>Diskon untuk pembelian aksesoris</li>
                            <li>Tukar poin member</li>
                            <li>Raih poin di hari ulang tahun Anda</li>
                            <li>Gratis Ongkos Pengiriman</li>
                            <li>Nikmati fasilitas Priority Lounge di Planet Gadget Store terdekat</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="pg-club-membership-banner">
            <h2>
                <span>Daftar Sekarang dan dapatkan </span><span class="gold-span">Gold Membership!</span>
            </h2>
            <div class="pg-action-wrapper">
                <a href="/customer/account/create/" class="action primary" ><span>DAFTAR SEKARANG</span></a>
            </div>
        </div>
        ';
        return [
            [
                'title' => 'Planet Gadget Club',
                'page_layout' => '1column',
                'identifier' => 'planet-gadget-club',
                'content_heading' => 'Planet Gadget Club',
                'content' => $pg_club_content,
                'url_key' => 'planet-gadget-club',
                'is_active' => 1,
                'store_id' => 0
            ],
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
