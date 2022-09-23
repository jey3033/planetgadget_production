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
 * Class CreateCmsStaticPages
 */
class CreateCmsStaticPages implements DataPatchInterface
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
                'title' => 'Welcome to our Exclusive Online Store',
                'page_layout' => '1column',
                'identifier' => 'private-sales',
                'content_heading' => 'Welcome to our Exclusive Online Store',
                'content' => '<div class="private-sales-index">
                    <div class="box">
                    <div class="content">
                    <h1>Welcome to our Exclusive Online Store</h1>
                    <p>If you are a registered member, please <a href="{{store url="customer/account/login"}}">sign in here</a>.</p>
                    </div>
                    </div>
                    </div>',
                'url_key' => 'private-sales',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Reward Points',
                'page_layout' => '1column',
                'identifier' => 'reward-points',
                'content_heading' => 'Reward Points',
                'content' => '<p>The Reward Points Program allows you to earn points for certain actions you take on the site.  Points are awarded based on making purchases and customer actions such as submitting reviews.</p>

                    <h2>Benefits of Reward Points for Registered Customers</h2>
                    <p>Once you register you will be able to earn and accrue reward points, which are then redeemable at time of purchase towards the cost of your order. Rewards are an added bonus to your shopping experience on the site and just one of the ways we thank you for being a loyal customer.</p>

                    <h2>Earning Reward Points</h2>
                    <p>Rewards can currently be earned for the following actions:</p>
                    <ul>
                    <li>Making purchases — every time you make a purchase you earn points based on the price of products purchased and these points are added to your Reward Points balance.</li>
                    <li>Registering on the site.</li>
                    <li>Subscribing to a newsletter for the first time.</li>
                    <li>Sending Invitations — Earn points by inviting your friends to join the site.</li>
                    <li>Converting Invitations to Customer — Earn points for every invitation you send out which leads to your friends registering on the site.</li>
                    <li>Converting Invitations to Order — Earn points for every invitation you send out which leads to a sale.</li>
                    <li>Review Submission — Earn points for submitting product reviews.</li>
                    </ul>

                    <h2>Reward Points Exchange Rates</h2>
                    <p>The value of reward points is determined by an exchange rate of both currency spent on products to points, and an exchange rate of points earned to currency for spending on future purchases.</p>

                    <h2>Redeeming Reward Points</h2>
                    <p>You can redeem your reward points at checkout. If you have accumulated enough points to redeem them you will have the option of using points as one of the payment methods.  The option to use reward points, as well as your balance and the monetary equivalent this balance, will be shown to you in the Payment Method area of the checkout.  Redeemable reward points can be used in conjunction with other payment methods such as credit cards, gift cards and more.</p>
                    <p><img src="{{view url="Magento_Reward::images/payment.png"}}" alt="Payment Information" /></p>

                    <h2>Reward Points Minimums and Maximums</h2>
                    <p>Reward points may be capped at a minimum value required for redemption.  If this option is selected you will not be able to use your reward points until you accrue a minimum number of points, at which point they will become available for redemption.</p>
                    <p>Reward points may also be capped at the maximum value of points which can be accrued. If this option is selected you will need to redeem your accrued points before you are able to earn more points.</p>

                    <h2>Managing My Reward Points</h2>
                    <p>You have the ability to view and manage your points through your <a href="{{store url="customer/account"}}">Customer Account</a>. From your account you will be able to view your total points (and currency equivalent), minimum needed to redeem, whether you have reached the maximum points limit and a cumulative history of points acquired, redeemed and lost. The history record will retain and display historical rates and currency for informational purposes. The history will also show you comprehensive informational messages regarding points, including expiration notifications.</p>
                    <p><img src="{{view url="Magento_Reward::images/my_account.png"}}" alt="My Account" /></p>

                    <h2>Reward Points Expiration</h2>
                    <p>Reward points can be set to expire. Points will expire in the order form which they were first earned.</p>
                    <p><strong>Note</strong>: You can sign up to receive email notifications each time your balance changes when you either earn, redeem or lose points, as well as point expiration notifications. This option is found in the <a href="{{store url="reward/customer/info"}}">Reward Points section</a> of the My Account area.</p>',
                'url_key' => 'reward-points',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'How To Order',
                'page_layout' => '1column',
                'identifier' => 'how-to-order',
                'content_heading' => 'How To Order',
                'content' => '<h1>HOW TO ORDER</h1>
                    <div id="accordion" class="accordion" data-mage-init="{
                            &quot;accordion&quot;:{
                                &quot;active&quot;: [0],
                                &quot;collapsible&quot;: true,
                                &quot;openedState&quot;: &quot;active&quot;,
                                &quot;multipleCollapsible&quot;: false
                            }}">
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Bagaimana cara berbelanja di Planetgadget.store?</div>
                    <div class="content" data-role="content"><p>Masuk Ke Account Planetgadget.store atau Register di Planetgadget.store setelah itu Cari dan pilih produk yang Anda inginkan
                    <br/>Klik tombol “Tambah ke Keranjang”
                    <br/>Keranjang di pojok kanan atas bertambah dan silakan klik tombol “Bayar Sekarang”
                    <br/>Pilih metode untuk mendapatkan barang yang Anda inginkan, kemudian klik tombol “Lanjut Ke Pembayaran”
                    <br/>Pilih metode pembayaran yang Anda inginkan, kemudian klik tombol “Bayar Sekarang”</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Saya belum menerima pesanan, apa yang harus saya lakukan?</div>
                    <div class="content" data-role="content"><p>Silakan Anda cek status pesanan Anda dengan cara :
                    <br/>Pilih menu “Lacak Pesanan”
                    <br/>Masukkan Email dan Nomor Pesanan Anda
                    <br/>Klik tombol “LACAK”</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Pesanan saya dibatalkan namun dana belum kembali, bagaimana?</div>
                    <div class="content" data-role="content"><p>Proses pengembalian dana membutuhkan waktu paling lama 14 (empat belas) hari kerja.</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Pesanan yang saya terima tidak sesuai, apa yang harus saya lakukan?</div>
                    <div class="content" data-role="content"><p>Apabila Anda menerima barang yang rusak/salah, Anda dapat segera menghubungi customer care di email <a href="mailto:cs@planetgadget.store">cs@planetgadget.store</a> atau hubungi nomor telepon <a href="tel:+628113988888">08113988888</a>.</p>
                    </div>
                    </div>
                    </div>',
                'url_key' => 'how-to-order',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<h1>How To Pay</h1>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            [
                'title' => 'Shipping Information',
                'page_layout' => '1column',
                'identifier' => 'shipping-information',
                'content_heading' => 'Shipping Information',
                'content' => '<h1> PENGIRIMAN </h1>
<div id="accordion" class="accordion" data-mage-init="{
        &quot;accordion&quot;:{
            &quot;active&quot;: [0],
            &quot;collapsible&quot;: true,
            &quot;openedState&quot;: &quot;active&quot;,
            &quot;multipleCollapsible&quot;: false
        }}">
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana jika saya salah memasukkan alamat tujuan pengiriman?</div>
<div class="content" data-role="content">
<p>Mengubah informasi alamat tujuan hanya bisa dilakukan jika Anda belum melakukan pembayaran pesanan. Jika Anda sudah membayar, maka perubahan alamat tidak dapat dilakukan.</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Mengapa nomor resi pengiriman saya tidak bisa dilacak?</div>
<div class="content" data-role="content">
<p>Jika nomor resi atas pesanan Anda tidak dapat dilacak melalui kurir yang telah Anda pilih, berikut ini adalah beberapa kemungkinan penyebab hal tersebut :
<ul>
<li><p>Pihak agen logistik belum memperbarui data resi pada sistemnya.
<li><p>Agen logistik yang digunakan penjual tidak sesuai dengan yang Anda pilih saat melakukan transaksi.
</ul>
<p>Apabila Anda sudah mengecek hal-hal diatas dan masih menemui kendala, silakan hubungi customer care di email cs@planetgadget.store atau hubungi nomor telepon : 08113988888</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Apa saja layanan pengiriman yang tersedia ?</div>
<div class="content" data-role="content">
<p> Gosend , JNE dan J&T .
<br/>
<br/>Batas waktu proses pesanan
<br/>Senin-Minggu 09.00 - 17.00 WIB
<br/>
<br/>Hari Libur Nasional
<br/>Kurir Sameday pukul 12.00 WIB
<br/>Pengiriman regular pukul 13.00 WIB </p>
<br/>
</div>
</div>
</div>',
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
                'title' => 'Welcome to our Exclusive Online Store',
                'page_layout' => '1column',
                'identifier' => 'private-sales',
                'content_heading' => 'Welcome to our Exclusive Online Store',
                'content' => '<div class="private-sales-index">
                    <div class="box">
                    <div class="content">
                    <h1>Welcome to our Exclusive Online Store</h1>
                    <p>If you are a registered member, please <a href="{{store url="customer/account/login"}}">sign in here</a>.</p>
                    </div>
                    </div>
                    </div>',
                'url_key' => 'private-sales',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Reward Points',
                'page_layout' => '1column',
                'identifier' => 'reward-points',
                'content_heading' => 'Reward Points',
                'content' => '<p>The Reward Points Program allows you to earn points for certain actions you take on the site.  Points are awarded based on making purchases and customer actions such as submitting reviews.</p>

                    <h2>Benefits of Reward Points for Registered Customers</h2>
                    <p>Once you register you will be able to earn and accrue reward points, which are then redeemable at time of purchase towards the cost of your order. Rewards are an added bonus to your shopping experience on the site and just one of the ways we thank you for being a loyal customer.</p>

                    <h2>Earning Reward Points</h2>
                    <p>Rewards can currently be earned for the following actions:</p>
                    <ul>
                    <li>Making purchases — every time you make a purchase you earn points based on the price of products purchased and these points are added to your Reward Points balance.</li>
                    <li>Registering on the site.</li>
                    <li>Subscribing to a newsletter for the first time.</li>
                    <li>Sending Invitations — Earn points by inviting your friends to join the site.</li>
                    <li>Converting Invitations to Customer — Earn points for every invitation you send out which leads to your friends registering on the site.</li>
                    <li>Converting Invitations to Order — Earn points for every invitation you send out which leads to a sale.</li>
                    <li>Review Submission — Earn points for submitting product reviews.</li>
                    </ul>

                    <h2>Reward Points Exchange Rates</h2>
                    <p>The value of reward points is determined by an exchange rate of both currency spent on products to points, and an exchange rate of points earned to currency for spending on future purchases.</p>

                    <h2>Redeeming Reward Points</h2>
                    <p>You can redeem your reward points at checkout. If you have accumulated enough points to redeem them you will have the option of using points as one of the payment methods.  The option to use reward points, as well as your balance and the monetary equivalent this balance, will be shown to you in the Payment Method area of the checkout.  Redeemable reward points can be used in conjunction with other payment methods such as credit cards, gift cards and more.</p>
                    <p><img src="{{view url="Magento_Reward::images/payment.png"}}" alt="Payment Information" /></p>

                    <h2>Reward Points Minimums and Maximums</h2>
                    <p>Reward points may be capped at a minimum value required for redemption.  If this option is selected you will not be able to use your reward points until you accrue a minimum number of points, at which point they will become available for redemption.</p>
                    <p>Reward points may also be capped at the maximum value of points which can be accrued. If this option is selected you will need to redeem your accrued points before you are able to earn more points.</p>

                    <h2>Managing My Reward Points</h2>
                    <p>You have the ability to view and manage your points through your <a href="{{store url="customer/account"}}">Customer Account</a>. From your account you will be able to view your total points (and currency equivalent), minimum needed to redeem, whether you have reached the maximum points limit and a cumulative history of points acquired, redeemed and lost. The history record will retain and display historical rates and currency for informational purposes. The history will also show you comprehensive informational messages regarding points, including expiration notifications.</p>
                    <p><img src="{{view url="Magento_Reward::images/my_account.png"}}" alt="My Account" /></p>

                    <h2>Reward Points Expiration</h2>
                    <p>Reward points can be set to expire. Points will expire in the order form which they were first earned.</p>
                    <p><strong>Note</strong>: You can sign up to receive email notifications each time your balance changes when you either earn, redeem or lose points, as well as point expiration notifications. This option is found in the <a href="{{store url="reward/customer/info"}}">Reward Points section</a> of the My Account area.</p>',
                'url_key' => 'reward-points',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'How To Order',
                'page_layout' => '1column',
                'identifier' => 'how-to-order',
                'content_heading' => 'How To Order',
                'content' => '<h1>HOW TO ORDER</h1>
                    <div id="accordion" class="accordion" data-mage-init="{
                            &quot;accordion&quot;:{
                                &quot;active&quot;: [0],
                                &quot;collapsible&quot;: true,
                                &quot;openedState&quot;: &quot;active&quot;,
                                &quot;multipleCollapsible&quot;: false
                            }}">
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Bagaimana cara berbelanja di Planetgadget.store?</div>
                    <div class="content" data-role="content"><p>Masuk Ke Account Planetgadget.store atau Register di Planetgadget.store setelah itu Cari dan pilih produk yang Anda inginkan
                    <br/>Klik tombol “Tambah ke Keranjang”
                    <br/>Keranjang di pojok kanan atas bertambah dan silakan klik tombol “Bayar Sekarang”
                    <br/>Pilih metode untuk mendapatkan barang yang Anda inginkan, kemudian klik tombol “Lanjut Ke Pembayaran”
                    <br/>Pilih metode pembayaran yang Anda inginkan, kemudian klik tombol “Bayar Sekarang”</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Saya belum menerima pesanan, apa yang harus saya lakukan?</div>
                    <div class="content" data-role="content"><p>Silakan Anda cek status pesanan Anda dengan cara :
                    <br/>Pilih menu “Lacak Pesanan”
                    <br/>Masukkan Email dan Nomor Pesanan Anda
                    <br/>Klik tombol “LACAK”</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Pesanan saya dibatalkan namun dana belum kembali, bagaimana?</div>
                    <div class="content" data-role="content"><p>Proses pengembalian dana membutuhkan waktu paling lama 14 (empat belas) hari kerja.</p>
                    </div>
                    </div>
                    <div class="collapsible-item" data-role="collapsible">
                    <div class="title" data-role="trigger">Question : Pesanan yang saya terima tidak sesuai, apa yang harus saya lakukan?</div>
                    <div class="content" data-role="content"><p>Apabila Anda menerima barang yang rusak/salah, Anda dapat segera menghubungi customer care di email <a href="mailto:cs@planetgadget.store">cs@planetgadget.store</a> atau hubungi nomor telepon <a href="tel:+628113988888">08113988888</a>.</p>
                    </div>
                    </div>
                    </div>',
                'url_key' => 'how-to-order',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<h1>How To Pay</h1>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            [
                'title' => 'Shipping Information',
                'page_layout' => '1column',
                'identifier' => 'shipping-information',
                'content_heading' => 'Shipping Information',
                'content' => '<h1> PENGIRIMAN </h1>
<div id="accordion" class="accordion" data-mage-init="{
        &quot;accordion&quot;:{
            &quot;active&quot;: [0],
            &quot;collapsible&quot;: true,
            &quot;openedState&quot;: &quot;active&quot;,
            &quot;multipleCollapsible&quot;: false
        }}">
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana jika saya salah memasukkan alamat tujuan pengiriman?</div>
<div class="content" data-role="content">
<p>Mengubah informasi alamat tujuan hanya bisa dilakukan jika Anda belum melakukan pembayaran pesanan. Jika Anda sudah membayar, maka perubahan alamat tidak dapat dilakukan.</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Mengapa nomor resi pengiriman saya tidak bisa dilacak?</div>
<div class="content" data-role="content">
<p>Jika nomor resi atas pesanan Anda tidak dapat dilacak melalui kurir yang telah Anda pilih, berikut ini adalah beberapa kemungkinan penyebab hal tersebut :
<ul>
<li><p>Pihak agen logistik belum memperbarui data resi pada sistemnya.
<li><p>Agen logistik yang digunakan penjual tidak sesuai dengan yang Anda pilih saat melakukan transaksi.
</ul>
<p>Apabila Anda sudah mengecek hal-hal diatas dan masih menemui kendala, silakan hubungi customer care di email cs@planetgadget.store atau hubungi nomor telepon : 08113988888</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Apa saja layanan pengiriman yang tersedia ?</div>
<div class="content" data-role="content">
<p> Gosend , JNE dan J&T .
<br/>
<br/>Batas waktu proses pesanan
<br/>Senin-Minggu 09.00 - 17.00 WIB
<br/>
<br/>Hari Libur Nasional
<br/>Kurir Sameday pukul 12.00 WIB
<br/>Pengiriman regular pukul 13.00 WIB </p>
<br/>
</div>
</div>
</div>',
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
