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
 * Class UpdateCmsStaticPages
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
                'content' => '<h1>How To Pay</h1>
<div id="accordion" class="accordion" data-mage-init="{
        &quot;accordion&quot;:{
            &quot;active&quot;: [0],
            &quot;collapsible&quot;: true,
            &quot;openedState&quot;: &quot;active&quot;,
            &quot;multipleCollapsible&quot;: false
        }}">
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Apa saja pilihan metode pembayaran yang tersedia?</div>
<div class="content" data-role="content">
<strong>Kartu Kredit (Full Payment) :</strong>
<p>Semua kartu kredit berlogo JCB, Visa, Master Card dan Amex.</p>
<strong>Kartu Kredit (Cicilan 0%) :</strong>
<p>Bank <!---BCA,---> BNI, BRI, <!---BSI, CIMB Niaga,---> Citi, <!---DBS,---> HSBC, Mandiri, <!---Mega,---> Maybank, OCBC NISP, Permata <!---dan Standard Chartered--->.</p>
<strong>Virtual Account :</strong>
<p>Bank BCA, BNI, BRI, <!---Danamon,---> Mandiri, <!---Maybank---> dan Permata.</p>
<strong>e-Payment :</strong>
<p><!---BCA KlikPay, iPay BNI, LinkAja,---> Gopay<!---, Ovo, Octo Clicks dan PermataNet--->.</p>
<strong>Cicilan TANPA Kartu Kredit :</strong>
<p>Akulaku. Indodana . Kredivo </p>
<strong>Tunai Gerai Retail : </strong>
<p>Alfamart dan Indomaret.</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran dengan Kartu Kredit (Bayar Penuh):</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Kartu Kredit (Bayar Penuh)- All Bank Credit Card Full Payment
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV dari kartu kredit yang Anda gunakan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran dengan Cicilan 0%:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan 0% - Kartu Kredit yang Anda inginkan Anda dapat menikmati
<li><p>fasilitas Cicilan 0% dengan minimum transaksi senilai Rp. 000.000
<li><p>Pilih Tenor Cicilan yang Anda inginkan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran dengan kartu kredit, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BCA Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BCA Virtual Account melalui ATM BCA:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu KE REK. BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui BCA Mobile:</strong>
<ol>
<li><p>Buka aplikasi BCA Mobile
<li><p>Pilih menu m-BCA
<li><p>Masukkan kode akses m-BCA Anda
<li><p>Pilih menu m-Transfer
<li><p>Pilih opsi BCA Virtual Account
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol OK
<li><p>Klik tombol Send pada sudut kanan atas aplikasi untuk melakukan transfer, kemudian masukkan nominal yang perlu Anda bayar, lalu pilih OK
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih OK
<li><p>Masukkan 6-digit PIN m-BCA Anda untuk otorisasi transaksi, lalu klik tombol OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui KlikBCA:</strong>
<ol>
<li><p>Login akun KlikBCA Anda
<li><p>Masukkan User ID dan PIN KlikBCA Anda
<li><p>Pilih menu TRANSFER DANA
<li><p>Pilih menu TRANSFER KE BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol LANJUTKAN
<li><p>Halaman konfirmasi transaksi akan muncul, periksa kembali detail konfirmasi transaksi lalu klik LANJUTKAN
<li><p>Masukkan respon keyBCA appli 1 dari token Anda, lalu klik KIRIM
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BRI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BRI Virtual Account melalui ATM BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu LAINNYA - BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mobile Banking BRI:</strong>
<ol>
<li><p>Buka aplikasi BRI Mobile Banking
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik OK
<li><p>Masukkan PIN BRI Anda
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Internet Banking BRI:</strong>
<ol>
<li><p>Login dengan akun internet banking BRI Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan password internet banking BRI Anda
<li><p>Masukkan mtoken internet banking BRI
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu KE REKENING BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : BRI (Kode Bank : 002)
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mini ATM/Mesin EDC BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu MINI ATM
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih BRI VIRTUAL ACCOUNT
<li><p>Masukkan PIN Anda, kemudian tekan OK
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BNI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BNI Virtual Account melalui ATM BNI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih MENU LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih jenis rekening yang akan Anda gunakan (contoh: Rekening Tabungan)
<li><p>Pilih menu VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui Mobile Banking BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI Mobile Banking
<li><p>Masukkan user ID dan MPIN Anda
<li><p>Klik menu TRANSFER lalu pilih VIRTUAL ACCOUNT BILLING
<li><p>Pada menu INPUT BARU, masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik LANJUT
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan password Anda untuk otentikasi transaksi
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui iBank Personal BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI SMS Banking
<li><p>Klik menu TRANSFER
<li><p>Klik opsi TRANSFER REKENING BNI
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal sesuai tagihan Anda. Nominal pembayaran yang berbeda tidak dapat diproses
<li><p>Klik PROSES, kemudian halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, lalu klik SETUJU
<li><p>Balas SMS dengan mengetik PIN sesuai perintah atau dapat langsung mengirim SMS dengan format: TRF[SPASI]NomorVA[SPASI]NOMINAL kirim ke 334Contoh: TRF 8277087781881551 35000
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui BNI SMS Banking:</strong>
<ol>
<li><p>Login akun BNI internet banking Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, lalu klik LANJUT
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar konfirmasi transaksi. Periksa kembali detil transaksi Anda, jika sudah benar masukkan PIN BNI e-secure pada token Anda, kemudian klik PROSES
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Mandiri Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Mandiri:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA INDONESIA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih menu BAYAR/BELI
<li><p>Pilih menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, lalu tekan BENAR
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar ATM. Pastikan nominal transaksi sesuai dengan tagihan, kemudian tekan YA
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM, periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui Mandiri Online (via Web/Apps):</strong>
<ol>
<li><p>Login dengan akun Mandiri Online Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian tekan LANJUT
<li><p>Untuk Mandiri Online dengan akses website, OTP (One Time Password) akan dikirimkan ke nomor handphone Anda yang telah terdaftar untuk fasilitas Mandiri Online. Masukkan OTP ke token untuk mendapat Challenge Code. Lalu masukkan Challenge Code pada akun Mandiri Online Anda, lalu klik LANJUT
<li><p>Sedangkan untuk Mandiri Online melalui aplikasi, setelah muncul konfirmasi transaksi, tekan KIRIM, kemudian masukkan 6 digit MPIN Anda. Setelah transaksi berhasil, akan muncul bukti transaksi yang dapat Anda unduh dan simpan
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ONLINE atau TRANSFER KE BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : Mandiri (Kode Bank : 008)
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Danamon Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Danamon:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu PEMBAYARAN, kemudian menu LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT.
<li><p>Masukkan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui D-Mobile:</strong>
<ol>
<li><p>Buka aplikasi D-Mobile / Danamon Mobile Banking
<li><p>Masukkan user ID dan password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu VIRTUAL ACCOUNT kemudian TAMBAH BILLER BARU PEMBAYARAN
<li><p>Masukan 16 digit nomor Danamon virtual account yang telah Anda terima dari Planetgadget.store
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Masukkan mPIN Anda lalu klik KONFIRMASI
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukan Kode Bank Tujuan : Danamon (Kode Bank : 011)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi, jika sudah benar, tekan YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Permata Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Permata Virtual Account melalui ATM Bank Permata:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN, kemudian menu PEMBAYARAN LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui Permata Mobile:</strong>
<ol>
<li><p>Buka aplikasi Permata Mobile
<li><p>Masukan User ID & Password Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui PermataNet:</strong>
<ol>
<li><p>Login akun PermataNet Anda
<li><p>Masukan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Klik VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukkan Kode Bank Tujuan: Permata (Kode Bank : 013)
<li><p>Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM, pastikan detil konfirmasi telah sesuai. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran Maybank Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Maybank Virtual Account melalui ATM Maybank :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu Pembayaran/Top Up Pulsa
<li><p>Pilih menu Virtual Account
<li><p>Masukkan nomor 7828XXXXXXXXXXXX (6 angka kode Virtual account)
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui Internet Banking Maybank (Maybank2u) :</strong>
<ol>
<li><p>Login pada website Maybank Personal Internet Banking (M2U).
<li><p>Pilih menu Transfer
<li><p>Pilih menu Maybank Virtual Account
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Halaman konfirmasi transaksi akan muncul pada layar, masukkan SMS token (TAC).
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui ATM Bank Lain (dalam jaringan ATM BERSAMA/ALTO/PRIMA) :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu ke Rekening Bank Lain
<li><p>Masukan Kode Bank Tujuan : Maybank(Kode Bank : 016)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran BCA KlikPay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran BCA KlikPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - BCA KlikPay
<li><p>Klik tombol BAYAR SEKARANG, Anda akan diarahkan ke website BCA KlikPay. Pastikan Anda telah registrasi dan aktivasi akun di BCA KlikPay
<li><p>Masukkan alamat email dan password Anda
<li><p>Setelah Anda berhasil login, detil transaksi akan muncul secara otomatis.
<li><p>Pada jenis pembayaran, Anda dapat memilih KlikPay atau KARTU KREDIT BCA. Pada jenis pembayaran BCA KlikPay, Anda dapat memilih sumber dana debit dari akun Klik BCA individu dan Kartu kredit yang berlogo BCA Card (Full Payment)
<li><p>Periksa kembali detil transaksi Anda pada aplikasi BCA KlikPay. Jika sudah benar, klik tombol KIRIM
<li><p>Tekan tombol KIRIM OTP. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada BCA KlikPay Anda
<li><p>Masukkan kode verifikasi OTP pada website BCA KlikPay, lalu klik BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>Syarat nominal pembayaran yang dapat diterima oleh BCA KlikPay minimal senilai Rp. 10,000 (Sepuluh Ribu Rupiah) dan maksimal senilai Rp. 100,000,000 (Seratus Juta Rupiah).</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi Gopay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Aplikasi GoPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - GoPay. Sebuah QR Code akan muncul pada layar PC. Jika saldo GoPay Anda kurang dari Rp. 10,000, maka QR Code tidak akan muncul
<li><p>Buka aplikasi GOJEK Anda, lalu pilih menu ‘SCAN QR’. Arahkan kamera handphone Anda ke layar PC untuk memindai QR Code, lalu klik CONFIRM
<li><p>Perhatikan kembali detail transaksi Anda pada aplikasi Gojek. Jika sudah benar, klik tombol PAY
<li><p>Masukkan 6 digit PIN GoPay Anda untuk melanjutkan proses pembayaran. Lalu, notifikasi ‘Payment Successful’ akan muncul pada layar handphone Anda.
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi OVO?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi OVO:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - OVO. Lalu masukkan nomor handphone yang telah terdaftar pada aplikasi OVO Anda
<li><p>Klik tombol BAYAR SEKARANG. Layar konfirmasi pembayaran OVO akan muncul
<li><p>Buka aplikasi OVO Anda, lalu masuk ke menu NOTIFIKASI untuk melakukan konfirmasi pembayaran dari Planetgadget.store
<li><p>Pilih metode pembayaran dengan: OVO CASH atau OVO POINT. Lalu klik tombol BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi LinkAja?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Link Aja melalui Apps:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - LinkAja
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi LinkAja. Konfirmasi pembayaran akan muncul pada layar. Perhatikan kembali detail pembayaran Anda, lalu klik KONFIRMASI
<li><p>Masukkan PIN LinkAja Anda
<li><p>Aplikasi akan menampilkan notifikasi transaksi Anda berhasil atau tidak
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi Octo Clicks?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Octo Clicks:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - Octo Clicks
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi Octo Clicks. Login dengan akun Octo Clicks Anda
<li><p>Detail pembayaran Anda akan muncul pada layar, pilih pembayaran dengan OCTO CLICKS
<li><p>Pastikan sumber dana Anda sudah benar, periksa kembali nominal pembayaran lalu klik tombol SEND SMS. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada Octo Clicks Anda
<li><p>Masukkan kode OTP pada kolom yang telah disediakan, lalu klik tombol PAY
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran dengan Aplikasi PermataNet?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi PermataNet:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - PermataNet
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi PermataNet. Login dengan akun PermataNet Anda
<li><p>Masukkan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Masukkan nominal pembayaran Anda. Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran melalui Alfamart dan Indomaret?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran melalui Alfamart atau Indomaret:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Untuk metode pembayaran Alfamart atau Indomaret hanya berlaku untuk nilai transaksi sebagai berikut:
<div class="table-wrapper">
<table><caption>Payment Over the Counter</caption>
<thead>
<tr>
<th><strong>Metode Pembayaran</strong></th>
<th><strong>Nominal Transaksi</strong></th>
</tr>
</thead>
<tbody>
<tr>
<th>Alfamart</th>
<td> Rp. 10,000 - Rp. 2,500,000</td>
</tr>
<tr>
<th>Indomaret</th>
<td> Rp. 10,000 - Rp. 5,000,000</td>
</tr>
</tbody>
</table>
</div>
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Metode Lain: Alfamart atau Indomaret
<li><p>Anda akan mendapatkan kode pembayaran yang muncul pada halaman pembayaran
<li><p>Kunjungi gerai Alfamart atau Indomaret terdekat
<li><p>Tunjukan kode pembayaran yang telah Anda terima dari Planetgadget.store pada kasir
<li><p>Lakukan pembayaran sesuai dengan nominal tagihan
<li><p>Simpan tanda terima/ struk sebagai bukti pembayaran Anda
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran maksimum 4 jam.</strong>
<br/><br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran tanpa kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode Pembayaran Akulaku / Kredivo / Indodana :</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan Tanpa Kartu Kredit- Akulaku / Kredivo / Indodana.
<li><p>Klik tombol BAYAR SEKARANG - PAY NOW. Anda akan diarahkan ke halaman Akulaku / Kredivo / Indodana
<li><p>Login akun Akulaku / Kredivo / Indodana Anda. Masukkan nomor handphone dan password Anda
<li><p>Pilih jangka waktu angsuran yang Anda inginkan
<li><p>Masukkan kode verikasi OTP (One Time Password) yang telah terkirim ke nomor handphone Anda, lalu klik tombol NEXT.
<li><p>Transaksi Anda berhasil.
</ol>
<strong>*Transaksi Anda akan gagal/dibatalkan oleh pihak Planetgadget.store apabila tidak melakukan pembayaran selama 1x24 jam.</strong>
</br><br/>
</div>
</div>
</div>',
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
                'content' => '<h1>How To Pay</h1>
<div id="accordion" class="accordion" data-mage-init="{
        &quot;accordion&quot;:{
            &quot;active&quot;: [0],
            &quot;collapsible&quot;: true,
            &quot;openedState&quot;: &quot;active&quot;,
            &quot;multipleCollapsible&quot;: false
        }}">
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Apa saja pilihan metode pembayaran yang tersedia?</div>
<div class="content" data-role="content">
<strong>Kartu Kredit (Full Payment) :</strong>
<p>Semua kartu kredit berlogo JCB, Visa, Master Card dan Amex.</p>
<strong>Kartu Kredit (Cicilan 0%) :</strong>
<p>Bank <!---BCA,---> BNI, BRI, <!---BSI, CIMB Niaga,---> Citi, <!---DBS,---> HSBC, Mandiri, <!---Mega,---> Maybank, OCBC NISP, Permata <!---dan Standard Chartered--->.</p>
<strong>Virtual Account :</strong>
<p>Bank BCA, BNI, BRI, <!---Danamon,---> Mandiri, <!---Maybank---> dan Permata.</p>
<strong>e-Payment :</strong>
<p><!---BCA KlikPay, iPay BNI, LinkAja,---> Gopay<!---, Ovo, Octo Clicks dan PermataNet--->.</p>
<strong>Cicilan TANPA Kartu Kredit :</strong>
<p>Akulaku. Indodana . Kredivo </p>
<strong>Tunai Gerai Retail : </strong>
<p>Alfamart dan Indomaret.</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran dengan Kartu Kredit (Bayar Penuh):</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Kartu Kredit (Bayar Penuh)- All Bank Credit Card Full Payment
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV dari kartu kredit yang Anda gunakan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran dengan Cicilan 0%:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan 0% - Kartu Kredit yang Anda inginkan Anda dapat menikmati
<li><p>fasilitas Cicilan 0% dengan minimum transaksi senilai Rp. 000.000
<li><p>Pilih Tenor Cicilan yang Anda inginkan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran dengan kartu kredit, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BCA Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BCA Virtual Account melalui ATM BCA:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu KE REK. BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui BCA Mobile:</strong>
<ol>
<li><p>Buka aplikasi BCA Mobile
<li><p>Pilih menu m-BCA
<li><p>Masukkan kode akses m-BCA Anda
<li><p>Pilih menu m-Transfer
<li><p>Pilih opsi BCA Virtual Account
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol OK
<li><p>Klik tombol Send pada sudut kanan atas aplikasi untuk melakukan transfer, kemudian masukkan nominal yang perlu Anda bayar, lalu pilih OK
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih OK
<li><p>Masukkan 6-digit PIN m-BCA Anda untuk otorisasi transaksi, lalu klik tombol OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui KlikBCA:</strong>
<ol>
<li><p>Login akun KlikBCA Anda
<li><p>Masukkan User ID dan PIN KlikBCA Anda
<li><p>Pilih menu TRANSFER DANA
<li><p>Pilih menu TRANSFER KE BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol LANJUTKAN
<li><p>Halaman konfirmasi transaksi akan muncul, periksa kembali detail konfirmasi transaksi lalu klik LANJUTKAN
<li><p>Masukkan respon keyBCA appli 1 dari token Anda, lalu klik KIRIM
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BRI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BRI Virtual Account melalui ATM BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu LAINNYA - BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mobile Banking BRI:</strong>
<ol>
<li><p>Buka aplikasi BRI Mobile Banking
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik OK
<li><p>Masukkan PIN BRI Anda
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Internet Banking BRI:</strong>
<ol>
<li><p>Login dengan akun internet banking BRI Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan password internet banking BRI Anda
<li><p>Masukkan mtoken internet banking BRI
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu KE REKENING BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : BRI (Kode Bank : 002)
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mini ATM/Mesin EDC BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu MINI ATM
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih BRI VIRTUAL ACCOUNT
<li><p>Masukkan PIN Anda, kemudian tekan OK
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BNI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BNI Virtual Account melalui ATM BNI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih MENU LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih jenis rekening yang akan Anda gunakan (contoh: Rekening Tabungan)
<li><p>Pilih menu VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui Mobile Banking BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI Mobile Banking
<li><p>Masukkan user ID dan MPIN Anda
<li><p>Klik menu TRANSFER lalu pilih VIRTUAL ACCOUNT BILLING
<li><p>Pada menu INPUT BARU, masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik LANJUT
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan password Anda untuk otentikasi transaksi
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui iBank Personal BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI SMS Banking
<li><p>Klik menu TRANSFER
<li><p>Klik opsi TRANSFER REKENING BNI
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal sesuai tagihan Anda. Nominal pembayaran yang berbeda tidak dapat diproses
<li><p>Klik PROSES, kemudian halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, lalu klik SETUJU
<li><p>Balas SMS dengan mengetik PIN sesuai perintah atau dapat langsung mengirim SMS dengan format: TRF[SPASI]NomorVA[SPASI]NOMINAL kirim ke 334Contoh: TRF 8277087781881551 35000
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui BNI SMS Banking:</strong>
<ol>
<li><p>Login akun BNI internet banking Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, lalu klik LANJUT
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar konfirmasi transaksi. Periksa kembali detil transaksi Anda, jika sudah benar masukkan PIN BNI e-secure pada token Anda, kemudian klik PROSES
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Mandiri Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Mandiri:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA INDONESIA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih menu BAYAR/BELI
<li><p>Pilih menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, lalu tekan BENAR
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar ATM. Pastikan nominal transaksi sesuai dengan tagihan, kemudian tekan YA
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM, periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui Mandiri Online (via Web/Apps):</strong>
<ol>
<li><p>Login dengan akun Mandiri Online Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian tekan LANJUT
<li><p>Untuk Mandiri Online dengan akses website, OTP (One Time Password) akan dikirimkan ke nomor handphone Anda yang telah terdaftar untuk fasilitas Mandiri Online. Masukkan OTP ke token untuk mendapat Challenge Code. Lalu masukkan Challenge Code pada akun Mandiri Online Anda, lalu klik LANJUT
<li><p>Sedangkan untuk Mandiri Online melalui aplikasi, setelah muncul konfirmasi transaksi, tekan KIRIM, kemudian masukkan 6 digit MPIN Anda. Setelah transaksi berhasil, akan muncul bukti transaksi yang dapat Anda unduh dan simpan
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ONLINE atau TRANSFER KE BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : Mandiri (Kode Bank : 008)
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Danamon Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Danamon:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu PEMBAYARAN, kemudian menu LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT.
<li><p>Masukkan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui D-Mobile:</strong>
<ol>
<li><p>Buka aplikasi D-Mobile / Danamon Mobile Banking
<li><p>Masukkan user ID dan password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu VIRTUAL ACCOUNT kemudian TAMBAH BILLER BARU PEMBAYARAN
<li><p>Masukan 16 digit nomor Danamon virtual account yang telah Anda terima dari Planetgadget.store
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Masukkan mPIN Anda lalu klik KONFIRMASI
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukan Kode Bank Tujuan : Danamon (Kode Bank : 011)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi, jika sudah benar, tekan YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Permata Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Permata Virtual Account melalui ATM Bank Permata:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN, kemudian menu PEMBAYARAN LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui Permata Mobile:</strong>
<ol>
<li><p>Buka aplikasi Permata Mobile
<li><p>Masukan User ID & Password Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui PermataNet:</strong>
<ol>
<li><p>Login akun PermataNet Anda
<li><p>Masukan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Klik VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukkan Kode Bank Tujuan: Permata (Kode Bank : 013)
<li><p>Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM, pastikan detil konfirmasi telah sesuai. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran Maybank Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Maybank Virtual Account melalui ATM Maybank :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu Pembayaran/Top Up Pulsa
<li><p>Pilih menu Virtual Account
<li><p>Masukkan nomor 7828XXXXXXXXXXXX (6 angka kode Virtual account)
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui Internet Banking Maybank (Maybank2u) :</strong>
<ol>
<li><p>Login pada website Maybank Personal Internet Banking (M2U).
<li><p>Pilih menu Transfer
<li><p>Pilih menu Maybank Virtual Account
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Halaman konfirmasi transaksi akan muncul pada layar, masukkan SMS token (TAC).
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui ATM Bank Lain (dalam jaringan ATM BERSAMA/ALTO/PRIMA) :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu ke Rekening Bank Lain
<li><p>Masukan Kode Bank Tujuan : Maybank(Kode Bank : 016)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran BCA KlikPay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran BCA KlikPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - BCA KlikPay
<li><p>Klik tombol BAYAR SEKARANG, Anda akan diarahkan ke website BCA KlikPay. Pastikan Anda telah registrasi dan aktivasi akun di BCA KlikPay
<li><p>Masukkan alamat email dan password Anda
<li><p>Setelah Anda berhasil login, detil transaksi akan muncul secara otomatis.
<li><p>Pada jenis pembayaran, Anda dapat memilih KlikPay atau KARTU KREDIT BCA. Pada jenis pembayaran BCA KlikPay, Anda dapat memilih sumber dana debit dari akun Klik BCA individu dan Kartu kredit yang berlogo BCA Card (Full Payment)
<li><p>Periksa kembali detil transaksi Anda pada aplikasi BCA KlikPay. Jika sudah benar, klik tombol KIRIM
<li><p>Tekan tombol KIRIM OTP. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada BCA KlikPay Anda
<li><p>Masukkan kode verifikasi OTP pada website BCA KlikPay, lalu klik BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>Syarat nominal pembayaran yang dapat diterima oleh BCA KlikPay minimal senilai Rp. 10,000 (Sepuluh Ribu Rupiah) dan maksimal senilai Rp. 100,000,000 (Seratus Juta Rupiah).</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi Gopay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Aplikasi GoPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - GoPay. Sebuah QR Code akan muncul pada layar PC. Jika saldo GoPay Anda kurang dari Rp. 10,000, maka QR Code tidak akan muncul
<li><p>Buka aplikasi GOJEK Anda, lalu pilih menu ‘SCAN QR’. Arahkan kamera handphone Anda ke layar PC untuk memindai QR Code, lalu klik CONFIRM
<li><p>Perhatikan kembali detail transaksi Anda pada aplikasi Gojek. Jika sudah benar, klik tombol PAY
<li><p>Masukkan 6 digit PIN GoPay Anda untuk melanjutkan proses pembayaran. Lalu, notifikasi ‘Payment Successful’ akan muncul pada layar handphone Anda.
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi OVO?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi OVO:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - OVO. Lalu masukkan nomor handphone yang telah terdaftar pada aplikasi OVO Anda
<li><p>Klik tombol BAYAR SEKARANG. Layar konfirmasi pembayaran OVO akan muncul
<li><p>Buka aplikasi OVO Anda, lalu masuk ke menu NOTIFIKASI untuk melakukan konfirmasi pembayaran dari Planetgadget.store
<li><p>Pilih metode pembayaran dengan: OVO CASH atau OVO POINT. Lalu klik tombol BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi LinkAja?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Link Aja melalui Apps:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - LinkAja
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi LinkAja. Konfirmasi pembayaran akan muncul pada layar. Perhatikan kembali detail pembayaran Anda, lalu klik KONFIRMASI
<li><p>Masukkan PIN LinkAja Anda
<li><p>Aplikasi akan menampilkan notifikasi transaksi Anda berhasil atau tidak
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi Octo Clicks?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Octo Clicks:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - Octo Clicks
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi Octo Clicks. Login dengan akun Octo Clicks Anda
<li><p>Detail pembayaran Anda akan muncul pada layar, pilih pembayaran dengan OCTO CLICKS
<li><p>Pastikan sumber dana Anda sudah benar, periksa kembali nominal pembayaran lalu klik tombol SEND SMS. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada Octo Clicks Anda
<li><p>Masukkan kode OTP pada kolom yang telah disediakan, lalu klik tombol PAY
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran dengan Aplikasi PermataNet?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi PermataNet:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - PermataNet
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi PermataNet. Login dengan akun PermataNet Anda
<li><p>Masukkan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Masukkan nominal pembayaran Anda. Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran melalui Alfamart dan Indomaret?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran melalui Alfamart atau Indomaret:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Untuk metode pembayaran Alfamart atau Indomaret hanya berlaku untuk nilai transaksi sebagai berikut:
<div class="table-wrapper">
<table><caption>Payment Over the Counter</caption>
<thead>
<tr>
<th><strong>Metode Pembayaran</strong></th>
<th><strong>Nominal Transaksi</strong></th>
</tr>
</thead>
<tbody>
<tr>
<th>Alfamart</th>
<td> Rp. 10,000 - Rp. 2,500,000</td>
</tr>
<tr>
<th>Indomaret</th>
<td> Rp. 10,000 - Rp. 5,000,000</td>
</tr>
</tbody>
</table>
</div>
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Metode Lain: Alfamart atau Indomaret
<li><p>Anda akan mendapatkan kode pembayaran yang muncul pada halaman pembayaran
<li><p>Kunjungi gerai Alfamart atau Indomaret terdekat
<li><p>Tunjukan kode pembayaran yang telah Anda terima dari Planetgadget.store pada kasir
<li><p>Lakukan pembayaran sesuai dengan nominal tagihan
<li><p>Simpan tanda terima/ struk sebagai bukti pembayaran Anda
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran maksimum 4 jam.</strong>
<br/><br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran tanpa kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode Pembayaran Akulaku / Kredivo / Indodana :</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan Tanpa Kartu Kredit- Akulaku / Kredivo / Indodana.
<li><p>Klik tombol BAYAR SEKARANG - PAY NOW. Anda akan diarahkan ke halaman Akulaku / Kredivo / Indodana
<li><p>Login akun Akulaku / Kredivo / Indodana Anda. Masukkan nomor handphone dan password Anda
<li><p>Pilih jangka waktu angsuran yang Anda inginkan
<li><p>Masukkan kode verikasi OTP (One Time Password) yang telah terkirim ke nomor handphone Anda, lalu klik tombol NEXT.
<li><p>Transaksi Anda berhasil.
</ol>
<strong>*Transaksi Anda akan gagal/dibatalkan oleh pihak Planetgadget.store apabila tidak melakukan pembayaran selama 1x24 jam.</strong>
</br><br/>
</div>
</div>
</div>',
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
                'store_id' => 0
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
                'store_id' => 0
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
                'store_id' => 0
            ],
            [
                'title' => 'How To Pay',
                'page_layout' => '1column',
                'identifier' => 'how-to-pay',
                'content_heading' => 'How To Pay',
                'content' => '<h1>How To Pay</h1>
<div id="accordion" class="accordion" data-mage-init="{
        &quot;accordion&quot;:{
            &quot;active&quot;: [0],
            &quot;collapsible&quot;: true,
            &quot;openedState&quot;: &quot;active&quot;,
            &quot;multipleCollapsible&quot;: false
        }}">
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Apa saja pilihan metode pembayaran yang tersedia?</div>
<div class="content" data-role="content">
<strong>Kartu Kredit (Full Payment) :</strong>
<p>Semua kartu kredit berlogo JCB, Visa, Master Card dan Amex.</p>
<strong>Kartu Kredit (Cicilan 0%) :</strong>
<p>Bank <!---BCA,---> BNI, BRI, <!---BSI, CIMB Niaga,---> Citi, <!---DBS,---> HSBC, Mandiri, <!---Mega,---> Maybank, OCBC NISP, Permata <!---dan Standard Chartered--->.</p>
<strong>Virtual Account :</strong>
<p>Bank BCA, BNI, BRI, <!---Danamon,---> Mandiri, <!---Maybank---> dan Permata.</p>
<strong>e-Payment :</strong>
<p><!---BCA KlikPay, iPay BNI, LinkAja,---> Gopay<!---, Ovo, Octo Clicks dan PermataNet--->.</p>
<strong>Cicilan TANPA Kartu Kredit :</strong>
<p>Akulaku. Indodana . Kredivo </p>
<strong>Tunai Gerai Retail : </strong>
<p>Alfamart dan Indomaret.</p>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran dengan Kartu Kredit (Bayar Penuh):</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Kartu Kredit (Bayar Penuh)- All Bank Credit Card Full Payment
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV dari kartu kredit yang Anda gunakan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran dengan Cicilan 0%:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan 0% - Kartu Kredit yang Anda inginkan Anda dapat menikmati
<li><p>fasilitas Cicilan 0% dengan minimum transaksi senilai Rp. 000.000
<li><p>Pilih Tenor Cicilan yang Anda inginkan
<li><p>Detil transaksi akan muncul pada layar Anda, klik CONTINUE jika ingin melanjutkan pembayaran
<li><p>Masukkan Nomor Kartu Kredit, Expiration Date dan CVV, lalu klik PAY NOW
<li><p>Mohon perhatikan kembali detil transaksi Anda. Jika sudah benar, lanjut ke halaman 3D Secure. Kode bayar OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada kartu kredit Anda
<li><p>Masukkan kode bayar OTP (One Time Password), lalu klik LANJUTKAN OK
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran dengan kartu kredit, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BCA Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BCA Virtual Account melalui ATM BCA:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu KE REK. BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui BCA Mobile:</strong>
<ol>
<li><p>Buka aplikasi BCA Mobile
<li><p>Pilih menu m-BCA
<li><p>Masukkan kode akses m-BCA Anda
<li><p>Pilih menu m-Transfer
<li><p>Pilih opsi BCA Virtual Account
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol OK
<li><p>Klik tombol Send pada sudut kanan atas aplikasi untuk melakukan transfer, kemudian masukkan nominal yang perlu Anda bayar, lalu pilih OK
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detail konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih OK
<li><p>Masukkan 6-digit PIN m-BCA Anda untuk otorisasi transaksi, lalu klik tombol OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BCA Virtual Account melalui KlikBCA:</strong>
<ol>
<li><p>Login akun KlikBCA Anda
<li><p>Masukkan User ID dan PIN KlikBCA Anda
<li><p>Pilih menu TRANSFER DANA
<li><p>Pilih menu TRANSFER KE BCA VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BCA Virtual Account yang telah Anda terima dari Planetgadget.store , lalu klik tombol LANJUTKAN
<li><p>Halaman konfirmasi transaksi akan muncul, periksa kembali detail konfirmasi transaksi lalu klik LANJUTKAN
<li><p>Masukkan respon keyBCA appli 1 dari token Anda, lalu klik KIRIM
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BRI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BRI Virtual Account melalui ATM BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu LAINNYA - BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mobile Banking BRI:</strong>
<ol>
<li><p>Buka aplikasi BRI Mobile Banking
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik OK
<li><p>Masukkan PIN BRI Anda
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Internet Banking BRI:</strong>
<ol>
<li><p>Login dengan akun internet banking BRI Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu BRI VIRTUAL ACCOUNT
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan password internet banking BRI Anda
<li><p>Masukkan mtoken internet banking BRI
<li><p>Periksa kembali detil konfirmasi transaksi Anda
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSAKSI LAINNYA
<li><p>Pilih menu KE REKENING BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : BRI (Kode Bank : 002)
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BRI Virtual Account melalui Mini ATM/Mesin EDC BRI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu MINI ATM
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih BRI VIRTUAL ACCOUNT
<li><p>Masukkan PIN Anda, kemudian tekan OK
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan OK untuk melanjutkan pembayaran
<li><p>Masukkan 16 digit nomor BRI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran BNI Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran BNI Virtual Account melalui ATM BNI:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih MENU LAINNYA
<li><p>Pilih menu TRANSFER
<li><p>Pilih jenis rekening yang akan Anda gunakan (contoh: Rekening Tabungan)
<li><p>Pilih menu VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukkan nominal yang perlu Anda bayar, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui Mobile Banking BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI Mobile Banking
<li><p>Masukkan user ID dan MPIN Anda
<li><p>Klik menu TRANSFER lalu pilih VIRTUAL ACCOUNT BILLING
<li><p>Pada menu INPUT BARU, masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Nominal pembayaran Anda akan muncul pada layar konfirmasi, pastikan nominal pembayaran sesuai dengan tagihan Anda, lalu klik LANJUT
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan password Anda untuk otentikasi transaksi
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui iBank Personal BNI:</strong>
<ol>
<li><p>Buka aplikasi BNI SMS Banking
<li><p>Klik menu TRANSFER
<li><p>Klik opsi TRANSFER REKENING BNI
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal sesuai tagihan Anda. Nominal pembayaran yang berbeda tidak dapat diproses
<li><p>Klik PROSES, kemudian halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, lalu klik SETUJU
<li><p>Balas SMS dengan mengetik PIN sesuai perintah atau dapat langsung mengirim SMS dengan format: TRF[SPASI]NomorVA[SPASI]NOMINAL kirim ke 334Contoh: TRF 8277087781881551 35000
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Metode pembayaran BNI Virtual Account melalui BNI SMS Banking:</strong>
<ol>
<li><p>Login akun BNI internet banking Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih VIRTUAL ACCOUNT BILLING
<li><p>Masukkan 16 digit nomor BNI Virtual Account yang telah Anda terima dari Planetgadget.store, lalu klik LANJUT
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar konfirmasi transaksi. Periksa kembali detil transaksi Anda, jika sudah benar masukkan PIN BNI e-secure pada token Anda, kemudian klik PROSES
<li><p>Transaksi Anda berhasil</p>
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Mandiri Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Mandiri:</strong>
<ol>
<li><p>Masukkan kartu ATM
<li><p>Pilih menu BAHASA INDONESIA
<li><p>Masukkan PIN ATM Anda
<li><p>Pilih menu BAYAR/BELI
<li><p>Pilih menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, lalu tekan BENAR
<li><p>Kemudian nominal tagihan Anda akan muncul pada layar ATM. Pastikan nominal transaksi sesuai dengan tagihan, kemudian tekan YA
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM, periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui Mandiri Online (via Web/Apps):</strong>
<ol>
<li><p>Login dengan akun Mandiri Online Anda
<li><p>Masukkan User ID dan Password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu MULTIPAYMENT
<li><p>Masukkan kode perusahaan, yaitu 70018-SPRINT lalu tekan BENAR, atau Klik DAFTAR KODE untuk mencari kode PT Sprint Asia, yaitu 70018 kemudian tekan BENAR
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian tekan LANJUT
<li><p>Untuk Mandiri Online dengan akses website, OTP (One Time Password) akan dikirimkan ke nomor handphone Anda yang telah terdaftar untuk fasilitas Mandiri Online. Masukkan OTP ke token untuk mendapat Challenge Code. Lalu masukkan Challenge Code pada akun Mandiri Online Anda, lalu klik LANJUT
<li><p>Sedangkan untuk Mandiri Online melalui aplikasi, setelah muncul konfirmasi transaksi, tekan KIRIM, kemudian masukkan 6 digit MPIN Anda. Setelah transaksi berhasil, akan muncul bukti transaksi yang dapat Anda unduh dan simpan
</ol><br/>
<strong>Metode pembayaran Mandiri Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ONLINE atau TRANSFER KE BANK LAIN
<li><p>Masukkan Kode Bank Tujuan : Mandiri (Kode Bank : 008)
<li><p>Masukkan 16 digit nomor Mandiri Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Perhatikan kembali detil konfirmasi transaksi Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Danamon Virtual Account?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Danamon:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu PEMBAYARAN, kemudian menu LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT.
<li><p>Masukkan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui D-Mobile:</strong>
<ol>
<li><p>Buka aplikasi D-Mobile / Danamon Mobile Banking
<li><p>Masukkan user ID dan password Anda
<li><p>Klik menu PEMBAYARAN
<li><p>Klik menu VIRTUAL ACCOUNT kemudian TAMBAH BILLER BARU PEMBAYARAN
<li><p>Masukan 16 digit nomor Danamon virtual account yang telah Anda terima dari Planetgadget.store
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Masukkan mPIN Anda lalu klik KONFIRMASI
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Danamon Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukan Kode Bank Tujuan : Danamon (Kode Bank : 011)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi, jika sudah benar, tekan YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.store dengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.store akan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Permata Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Permata Virtual Account melalui ATM Bank Permata:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih TRANSAKSI LAINNYA
<li><p>Pilih menu PEMBAYARAN, kemudian menu PEMBAYARAN LAINNYA
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store, lalu pilih BENAR
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA / LANJUT untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui Permata Mobile:</strong>
<ol>
<li><p>Buka aplikasi Permata Mobile
<li><p>Masukan User ID & Password Anda
<li><p>Pilih menu PEMBAYARAN
<li><p>Pilih menu VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui PermataNet:</strong>
<ol>
<li><p>Login akun PermataNet Anda
<li><p>Masukan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Klik VIRTUAL ACCOUNT. Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukkan nominal pembayaran Anda
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Permata Virtual Account melalui ATM Bank Lain:</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER ANTAR BANK
<li><p>Masukkan Kode Bank Tujuan: Permata (Kode Bank : 013)
<li><p>Masukkan 16 digit nomor Permata Virtual Account yang telah Anda terima dari Planetgadget.store
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Konfirmasi transaksi Anda akan muncul pada layar ATM, pastikan detil konfirmasi telah sesuai. Jika sudah benar, tekan YA untuk melanjutkan pembayaran
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran Maybank Virtual Account?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Maybank Virtual Account melalui ATM Maybank :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu Pembayaran/Top Up Pulsa
<li><p>Pilih menu Virtual Account
<li><p>Masukkan nomor 7828XXXXXXXXXXXX (6 angka kode Virtual account)
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui Internet Banking Maybank (Maybank2u) :</strong>
<ol>
<li><p>Login pada website Maybank Personal Internet Banking (M2U).
<li><p>Pilih menu Transfer
<li><p>Pilih menu Maybank Virtual Account
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda
<li><p>Halaman konfirmasi transaksi akan muncul pada layar, masukkan SMS token (TAC).
<li><p>Transaksi Anda berhasil
</ol><br/>
<strong>Metode pembayaran Maybank Virtual Account melalui ATM Bank Lain (dalam jaringan ATM BERSAMA/ALTO/PRIMA) :</strong>
<ol>
<li><p>Masukkan kartu ATM dan PIN Anda
<li><p>Pilih menu TRANSFER
<li><p>Pilih menu ke Rekening Bank Lain
<li><p>Masukan Kode Bank Tujuan : Maybank(Kode Bank : 016)
<li><p>Masukan 16 digit nomor Danamon Virtual Account yang telah Anda terima dari Planetgadget.store, kemudian pilih BENAR
<li><p>Masukan nominal pembayaran yang perlu Anda bayar
<li><p>Halaman konfirmasi transaksi akan muncul pada layar ATM. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, kemudian pilih YA
<li><p>Transaksi Anda Berhasil
</ol><br/>
<strong>Catatan Penting:</strong>
<ul>
<li><p>Batas waktu pembayaran dilakukan maksimum 2 jam setelah proses transaksi pembelian selesai
<li><p>Jika Konsumen telah melakukan metode pembayaran Virtual Account, namun status transaksi di Planetgadget.store tercatat CANCEL; harap segera menghubungi Customer Service Planetgadget.storedengan menyertakan bukti pembayaran atau transfer. Pihak dari Planetgadget.storeakan melakukan pengecekan pada bank terkait dalam waktu 1x24 jam pada hari kerja
</ul>
<br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran BCA KlikPay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran BCA KlikPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - BCA KlikPay
<li><p>Klik tombol BAYAR SEKARANG, Anda akan diarahkan ke website BCA KlikPay. Pastikan Anda telah registrasi dan aktivasi akun di BCA KlikPay
<li><p>Masukkan alamat email dan password Anda
<li><p>Setelah Anda berhasil login, detil transaksi akan muncul secara otomatis.
<li><p>Pada jenis pembayaran, Anda dapat memilih KlikPay atau KARTU KREDIT BCA. Pada jenis pembayaran BCA KlikPay, Anda dapat memilih sumber dana debit dari akun Klik BCA individu dan Kartu kredit yang berlogo BCA Card (Full Payment)
<li><p>Periksa kembali detil transaksi Anda pada aplikasi BCA KlikPay. Jika sudah benar, klik tombol KIRIM
<li><p>Tekan tombol KIRIM OTP. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada BCA KlikPay Anda
<li><p>Masukkan kode verifikasi OTP pada website BCA KlikPay, lalu klik BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>Syarat nominal pembayaran yang dapat diterima oleh BCA KlikPay minimal senilai Rp. 10,000 (Sepuluh Ribu Rupiah) dan maksimal senilai Rp. 100,000,000 (Seratus Juta Rupiah).</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi Gopay?</div>
<div class="content" data-role="content">
<strong> Metode pembayaran Aplikasi GoPay:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - GoPay. Sebuah QR Code akan muncul pada layar PC. Jika saldo GoPay Anda kurang dari Rp. 10,000, maka QR Code tidak akan muncul
<li><p>Buka aplikasi GOJEK Anda, lalu pilih menu ‘SCAN QR’. Arahkan kamera handphone Anda ke layar PC untuk memindai QR Code, lalu klik CONFIRM
<li><p>Perhatikan kembali detail transaksi Anda pada aplikasi Gojek. Jika sudah benar, klik tombol PAY
<li><p>Masukkan 6 digit PIN GoPay Anda untuk melanjutkan proses pembayaran. Lalu, notifikasi ‘Payment Successful’ akan muncul pada layar handphone Anda.
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran Aplikasi OVO?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi OVO:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - OVO. Lalu masukkan nomor handphone yang telah terdaftar pada aplikasi OVO Anda
<li><p>Klik tombol BAYAR SEKARANG. Layar konfirmasi pembayaran OVO akan muncul
<li><p>Buka aplikasi OVO Anda, lalu masuk ke menu NOTIFIKASI untuk melakukan konfirmasi pembayaran dari Planetgadget.store
<li><p>Pilih metode pembayaran dengan: OVO CASH atau OVO POINT. Lalu klik tombol BAYAR
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran 30 detik.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi LinkAja?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Link Aja melalui Apps:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - LinkAja
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi LinkAja. Konfirmasi pembayaran akan muncul pada layar. Perhatikan kembali detail pembayaran Anda, lalu klik KONFIRMASI
<li><p>Masukkan PIN LinkAja Anda
<li><p>Aplikasi akan menampilkan notifikasi transaksi Anda berhasil atau tidak
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran dengan Aplikasi Octo Clicks?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi Octo Clicks:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - Octo Clicks
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi Octo Clicks. Login dengan akun Octo Clicks Anda
<li><p>Detail pembayaran Anda akan muncul pada layar, pilih pembayaran dengan OCTO CLICKS
<li><p>Pastikan sumber dana Anda sudah benar, periksa kembali nominal pembayaran lalu klik tombol SEND SMS. Kode verifikasi OTP (One Time Password) akan dikirimkan ke nomor handphone yang telah terdaftar pada Octo Clicks Anda
<li><p>Masukkan kode OTP pada kolom yang telah disediakan, lalu klik tombol PAY
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<!---<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran dengan Aplikasi PermataNet?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran Aplikasi PermataNet:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih ePayment - PermataNet
<li><p>Klik tombol BAYAR SEKARANG
<li><p>Anda akan diarahkan langsung ke aplikasi PermataNet. Login dengan akun PermataNet Anda
<li><p>Masukkan User ID & Password Anda
<li><p>Klik menu PEMBAYARAN TAGIHAN
<li><p>Masukkan nominal pembayaran Anda. Halaman konfirmasi transaksi akan muncul. Periksa kembali detil konfirmasi transaksi Anda. Jika sudah benar, masukkan otentikasi transaksi pada token Anda
<li><p>Transaksi Anda berhasil
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran yang telah ditentukan.</strong>
<br/><br/>
</div>
</div>--->
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger"> Question : Bagaimana cara melakukan pembayaran melalui Alfamart dan Indomaret?</div>
<div class="content" data-role="content">
<strong>Metode pembayaran melalui Alfamart atau Indomaret:</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Untuk metode pembayaran Alfamart atau Indomaret hanya berlaku untuk nilai transaksi sebagai berikut:
<div class="table-wrapper">
<table><caption>Payment Over the Counter</caption>
<thead>
<tr>
<th><strong>Metode Pembayaran</strong></th>
<th><strong>Nominal Transaksi</strong></th>
</tr>
</thead>
<tbody>
<tr>
<th>Alfamart</th>
<td> Rp. 10,000 - Rp. 2,500,000</td>
</tr>
<tr>
<th>Indomaret</th>
<td> Rp. 10,000 - Rp. 5,000,000</td>
</tr>
</tbody>
</table>
</div>
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Metode Lain: Alfamart atau Indomaret
<li><p>Anda akan mendapatkan kode pembayaran yang muncul pada halaman pembayaran
<li><p>Kunjungi gerai Alfamart atau Indomaret terdekat
<li><p>Tunjukan kode pembayaran yang telah Anda terima dari Planetgadget.store pada kasir
<li><p>Lakukan pembayaran sesuai dengan nominal tagihan
<li><p>Simpan tanda terima/ struk sebagai bukti pembayaran Anda
</ol>
<strong>*Proses pembayaran Anda akan gagal jika melebihi batas waktu validasi pembayaran maksimum 4 jam.</strong>
<br/><br/>
</div>
</div>
<div class="collapsible-item" data-role="collapsible">
<div class="title" data-role="trigger">Question : Bagaimana cara melakukan pembayaran tanpa kartu kredit?</div>
<div class="content" data-role="content">
<strong>Metode Pembayaran Akulaku / Kredivo / Indodana :</strong>
<ol>
<li><p>Pilih pesanan atau barang yang Anda inginkan
<li><p>Klik tombol TAMBAH KERANJANG yang terletak di kanan atas pada menu KERANJANG
<li><p>Klik tombol LANJUT KE CHECKOUT untuk melanjutkan proses pesanan Anda
<li><p>Klik tombol LANJUT KE PEMBAYARAN untuk melakukan proses pembayaran
<li><p>Pada halaman metode pembayaran, pilih Cicilan Tanpa Kartu Kredit- Akulaku / Kredivo / Indodana.
<li><p>Klik tombol BAYAR SEKARANG - PAY NOW. Anda akan diarahkan ke halaman Akulaku / Kredivo / Indodana
<li><p>Login akun Akulaku / Kredivo / Indodana Anda. Masukkan nomor handphone dan password Anda
<li><p>Pilih jangka waktu angsuran yang Anda inginkan
<li><p>Masukkan kode verikasi OTP (One Time Password) yang telah terkirim ke nomor handphone Anda, lalu klik tombol NEXT.
<li><p>Transaksi Anda berhasil.
</ol>
<strong>*Transaksi Anda akan gagal/dibatalkan oleh pihak Planetgadget.store apabila tidak melakukan pembayaran selama 1x24 jam.</strong>
</br><br/>
</div>
</div>
</div>',
                'url_key' => 'how-to-pay',
                'is_active' => 1,
                'store_id' => 0
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
