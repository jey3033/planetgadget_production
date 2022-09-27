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
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UpdateBlocksBeforeFirstCloudDeployment
 */
class UpdateBlocksBeforeFirstCloudDeployment implements DataPatchInterface
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param BlockFactory $blockFactory
     * @param StoreManagerInterface $storeManager
     * @param HelperData $helperData
     */
    public function __construct(
        BlockFactory          $blockFactory,
        StoreManagerInterface $storeManager,
        HelperData            $helperData
    )
    {
        $this->blockFactory = $blockFactory;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
    }

    /**
     * @return CreateHomeCmsBlocks|void
     */
    public function apply()
    {
        //$storeID = HelperData::PG_STORE_ID;
        $blocksData = $this->getBlockData();

        foreach ($blocksData as $identifier => $block) {

            $getBlock = $this->blockFactory->create()
                ->getCollection()
                ->addFieldToFilter('identifier', $identifier)
                ->addFieldToFilter('store_id', $block['store_id'])
                ->getLastItem();

            if (empty($getBlock->getData())) {
                $this->helperData->createBlock($identifier, $block['title'], $block['store_id'], $block['content']);
            } else {
                $this->helperData->updateBlock($getBlock->getData('block_id'), $block['store_id'], $block['content']);
            }
        }

    }

    /**
     * Get CMS Block Data
     * @return array[]
     */
    public function getBlockData(): array
    {
        return [
            "checkout-footer" => [
                'title' => 'Checkout Footer Copyright',
                'content' => '<div class="copyright">
<p>© 2020 {{config path="general/store_information/name"}}. All Rights Reserved.</p>
</div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "kemana_contact_map_block" => [
                'title' => 'Contact Us Address Block',
                'content' => '<div class="block-title"><strong>{{config path="general/store_information/name"}}</strong></div>
<p>{{config path="general/store_information/street_line1"}} {{config path="general/store_information/city"}} {{config path="general/store_information/postcode"}}</p>
<div class="working-hours">
<div class="title">Working Hours</div>
<ul>
<li>Monday - Friday <br>{{config path="general/store_information/hours"}}</li>
<li>Phone: {{config path="general/store_information/phone"}} <br>Email: <a href="mailto:{{config path="trans_email/ident_general/email"}}">{{config path="trans_email/ident_general/email"}}</a></li>
</ul>
</div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "email-bottom-content" => [
                'title' => 'Email Footer Bottom',
                'content' => '<div class="footer-store-name">{{config path="general/store_information/name"}}</div>
<div class="footer-store-address">{{config path="general/store_information/street_line1"}}{{config path="general/store_information/street_line2"}}<br>{{config path="general/store_information/country_id"}}</div>
<ul class="footer-social-icons">
<li class="facebook"><a href="#" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/email/facebook.png}}" alt=""></a></li>
<li class="twitter"><a href="#" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/email/twitter.png}}" alt=""></a></li>
<li class="google"><a href="#" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/email/google.png}}" alt=""></a></li>
</ul>
<div class="footer-copyright">© {{config path="general/store_information/name"}} 2021</div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "footer-address-section" => [
                'title' => 'Footer Address Section',
                'content' => '<div class="logo"><img src="{{media url=wysiwyg/footer/Planet_Gadget_Footer_2x.png}}" alt=""></div>
<div class="customer-service">Please contact us through<br/>the contact information below:</div>
<div class="customer-service-info">Operational Hours: <span class="hours">09:00 - 21:00</span></div>
<div class="email"><a href="mailto:cs@planetgadget.store">{{config path="trans_email/ident_general/email"}}</a></div>
<div class="number"><a href="tel:{{config path="general/store_information/phone"}}">{{config path="general/store_information/phone"}}</a></div>
<div class="live-chat"><a href="https://wa.me/628113988888">Chat with us!</a></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "footer-information-links-section" => [
                'title' => 'Footer Information Links Section',
                'content' => '<p class="title" data-role="collapsible">Information</p>
<ul class="list">
	<li><a href="{{store url=\'about-us\'}}">About Us</a></li>
	<!---<li><a href="{{store url=\'customer-service\'}}">Customer Service</a></li>--->
	<li><a href="{{store url=\'contact\'}}">Contact Us</a></li>
	<li><a href="{{store url=\'terms-and-conditions\'}}">Privacy and Terms</a></li>
	<li><a href="{{store url=\'store-locations\'}}">Store Locator</a></li>
	<li><a href="{{store url=\'job\'}}">Career</a></li>
	<li><a href="{{store url=\'corporate-order\'}}">Corporate Order</a></li>
	<li><a href="{{store url=\'blog\'}}">News and Updates</a></li>
	<li><a href="{{store url=\'trade-in-plus\'}}">Trade in Plus</a></li>
	<!---<li><a href="{{store url=\'faq\'}}">FAQ</a></li>
	<li><a href="{{store url=\'how-to-order\'}}">How to Order</a></li>
	<li><a href="{{store url=\'how-to-pay\'}}">How to Pay</a></li>
	<li><a href="{{store url=\'shipping-information\'}}">Shipping Information</a></li>
	<li><a href="{{store url=\'return-policy\'}}">Return Policy</a></li>
	<li><a href="{{store url=\'warranty-policy\'}}">Warranty Policy</a></li>
	<li><a href="#">Lacak Pengiriman</a></li>
	<li><a href="{{store url=\'bank-promotion\'}}">Bank Promo</a></li>--->
</ul>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "footer-icons-section" => [
                'title' => 'Footer Icons Section',
                'content' => '<div class="item payment delivery">
	<p class="title">Delivery Support</p>
	<ul class="list payment-partners">
		<li class="gosend"><img src="{{media url=wysiwyg/footer/logos/gosend_2x.png}}" alt="" /></li>
		<!---<li class="grab"><img src="{{media url=wysiwyg/footer/logos/grab_2x.png}}" alt="" /></li>--->
		<li class="jne"><img src="{{media url=wysiwyg/footer/logos/jne_2x.png}}" alt="" /></li>
		<li class="jnt"><img src="{{media url=wysiwyg/footer/logos/jnt_2x.png}}" alt="" /></li>
		<!---<li class="sicepat"><img src="{{media url=wysiwyg/footer/logos/sicepat_2x.png}}" alt="" /></li>--->
		<!---<li class="anteraja"><img src="{{media url=wysiwyg/footer/logos/anteraja_2x.png}}" alt="" /></li>--->
		<!---<li class="ninja-express"><img src="{{media url=wysiwyg/footer/logos/ninjaexpress_2x.png}}" alt="" /></li>--->
		<li class="home-delivery"><img src="{{media url=wysiwyg/footer/logos/homedelivery_2x.png}}" alt="" /></li>
	</ul>
</div>
<div class="item payment">
	<p class="title">We Accept</p>
	<ul class="link-trademark">
		<li class="mandiri"><img src="{{media url=wysiwyg/footer/logos/mandiri_2x.png}}" alt="" /></li>
		<li class="bca"><img src="{{media url=wysiwyg/footer/logos/bca_2x.png}}" alt="" /></li>
		<li class="bni"><img src="{{media url=wysiwyg/footer/logos/bni_2x.png}}" alt="" /></li>
		<li class="bri"><img src="{{media url=wysiwyg/footer/logos/bri_2x.png}}" alt="" /></li>
		<li class="cimb"><img src="{{media url=wysiwyg/footer/logos/cimb_2x.png}}" alt="" /></li>
		<li class="permata"><img src="{{media url=wysiwyg/footer/logos/permata_2x.png}}" alt="" /></li>
		<li class="gopay"><img src="{{media url=wysiwyg/footer/logos/gopay_2x.png}}" alt="" /></li>
		<li class="shopeepay"><img src="{{media url=wysiwyg/footer/logos/shopeepay_2x.png}}" alt="" /></li>
		<li class="akulaku"><img src="{{media url=wysiwyg/footer/logos/akulaku_2x.png}}" alt="" /></li>
		<li class="kredivo"><img src="{{media url=wysiwyg/footer/logos/kredivo_2x.png}}" alt="" /></li>
		<!---<li class="home-credit"><img src="{{media url=wysiwyg/footer/logos/home_credit_2x.png}}" alt="" /></li>--->
		<li class="visa"><img src="{{media url=wysiwyg/footer/logos/visa_2x.png}}" alt="" /></li>
		<li class="mastercard"><img src="{{media url=wysiwyg/footer/logos/mastercard_2x.png}}" alt="" /></li>
		<li class="jcb"><img src="{{media url=wysiwyg/footer/logos/jcb_2x.png}}" alt="" /></li>
		<li class="indodana"><img src="{{media url=wysiwyg/footer/logos/indodana_2x.png}}" alt="" /></li>
		<!---<li class="indomaret"><img src="{{media url=wysiwyg/footer/logos/indomaret_2x.png}}" alt="" /></li>--->
		<li class="alfamart"><img src="{{media url=wysiwyg/footer/logos/alfamart_2x.png}}" alt="" /></li>
		<!---<li class="dandan"><img src="{{media url=wysiwyg/footer/logos/dandan_2x.png}}" alt="" /></li>--->
	</ul>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "home-banner-small-hardware" => [
                'title' => 'Home page small banners - Hardware Theme',
                'content' => '<div class="home-banner-two-container middle-banner-wrapper">
<div class="widget block block-static-block">
<div class="fixed-width">
<div class="half-width-banner">
<div class="banner-img"><img class="desktop" src="{{media url=wysiwyg/home-second-banner-hardware-1.jpg}}" alt=""> <img class="mobile" src="{{media url=wysiwyg/home-second-banner-hardware-1.jpg}}" alt=""></div>
<div class="banner-description color-white left">
<div class="banner-description-container">
<div class="banner-logo"><img src="{{media url=wysiwyg/home-second-banner-hardware-1-logo.png}}" alt=""></div>
<p>The right tool to do the job.</p>
<div class="actions-toolbar">
<div class="primary"><a class="action shop-now primary" href="#">SHOP NOW</a></div>
</div>
</div>
</div>
</div>
<div class="half-width-banner">
<div class="banner-img"><img class="desktop" src="{{media url=wysiwyg/home-second-banner-hardware-2.jpg}}" alt=""> <img class="mobile" src="{{media url=wysiwyg/home-second-banner-hardware-2.jpg}}" alt=""></div>
<div class="banner-description color-black right">
<div class="banner-description-container">
<div class="banner-logo"><img src="{{media url=wysiwyg/home-second-banner-hardware-2-logo.png}}" alt=""></div>
<p>Genuine Power Tools</p>
<div class="actions-toolbar">
<div class="primary"><a class="action shop-now primary" href="#">SHOP NOW</a></div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "home-page-new-arrivals" => [
                'title' => 'Home Page New Arrivals',
                'content' => '<div class="home-new-arrivals-wrapper"><div class="home-new-arrivals-container fixed-width">{{widget type="Magento\Catalog\Block\Product\Widget\NewWidget" title="New <span>Arrivals</span>" display_type="all_products" show_pager="0" products_count="10" template="product/widget/new/content/new_grid.phtml" cache_lifetime="2"}}</div></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "home-page-how-it-works" => [
                'title' => 'Home Page How It Works',
                'content' => '<div class="how-it-works fixed-width">
	<div class="row">
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Garansi_Resmi_2x.png}}" alt="" /></span>
		    <h5>100% Original</h5>
		    <p>All of the Products are TAM certified</p>
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Benefit_Berbelanja_2x.png}}" alt="" /></span>
		    <h5>Shopping Benefits</h5>
		    <p>Get Various Promos and Gadget Info</p>    
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Pengiriman_Terpercaya_2x.png}}" alt="" /></span>
		    <h5>Secure Delivery</h5>
		    <p>Fast, Safe & Reliable Delivery</p>
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Customer_Service_2x.png}}" alt="" /></span>
		    <h5>Customer Service</h5>
		    <p>We are ready to help you</p>
		</div>
	    </div>
	</div>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "home-page-inner-banner" => [
                'title' => 'Home Page Inner Banner',
                'content' => '<div class="home-banner-first-container">
    <div class="banner-inner-container">
                                <div class="row">
                                    <div class="col-md-6 col-12 banner-block">
                                        <div class="banner-img"><a href="https://pg.kemana.dev/category/page/detail/id/102" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/home/banners/Logitech_2x.png}}" alt="" /></a></div>
                                    </div>
                                    <div class="col-md-6 col-12 banner-block">
                                        <div class="banner-img"><a href="https://pg.kemana.dev/category/page/detail/id/28" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/home/banners/Oppo_F17_Pro_2x.png}}" alt="" /></a></div>
                                    </div>
                                </div>        
    </div>
</div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "home-page-bestsellers" => [
                'title' => 'Home Page Bestsellers',
                'content' => '<div class="home-best-seller-container fixed-width">{{widget type="Kemana\AcceleratorBase\Block\Catalog\Product\ProductsList" title="Gadget <span>Bestsellers</span>" widget_type="bestsellers" collection_sort_by="name" collection_sort_order="asc" show_pager="0" products_count="10" template="Kemana_AcceleratorBase::product/widget/content/grid.phtml" products_per_row="4" visible_products="4" visible_products_tablet="3" visible_products_mobile="1" pagination="false" widget_id="2947" conditions_encoded="^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^]^]"}}</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "home-page-recently-viewed-products" => [
                'title' => 'Home Page Recently Viewed Products',
                'content' => '<div class="recently-viewed-products-container">
                                <div class="fixed-width">{{widget type="Magento\Catalog\Block\Widget\RecentlyViewed" uiComponent="widget_recently_viewed" page_size="12" show_attributes="image" show_buttons="add_to_wishlist" template="product/widget/viewed/grid.phtml"}}</div>
                              </div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "home-page-full-width-banner" => [
                'title' => 'Home Page Full Width Banner',
                'content' => '<div class="home-banner-second-container ">
                                <div class="full-width-banner">
                                    <div class="banner-img">
                                        <img src="{{media url=wysiwyg/home-second-banner.png}}" alt="">
                                    </div>
                                    <div class="banner-description color-blue">
                                        <h4>NICE OUTFIT! GO OUTSIDE!</h4>
                                        <p>Try our Outfit!</p>
                                        <div class="actions-toolbar">
                                            <div class="primary">
                                                <a class="action shop-now primary" href="#">SHOP NOW</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "samsung-block" => [
                'title' => 'Samsung CMS Block',
                'content' => '<div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <img src=""/>
                        <div class="title">
                            <h1><strong>Refrigators</strong></h1>
                        </div>
                        <div class="description">
                            <span>Keep food fresher longer twice as long or more!</span>
                        </div>        
                        <div class="btn shop-now">
                            <a href="#" class="btn btn-default">Shop Now</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <img src=""/>
                        <div class="title">
                            <h1><strong>Oven Microwaves</strong></h1>
                        </div>
                        <div class="description">
                            <span>Rethink how you cook with PowerGrill technology</span>
                        </div>        
                        <div class="btn shop-now">
                            <a href="#" class="btn btn-default">Shop Now</a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <img src=""/>
                        <div class="title">
                            <h1><strong>QLED TV</strong></h1>
                        </div>
                        <div class="description">
                            <span>Television with Quantum Dot technology and 100% color volume</span>
                        </div>        
                        <div class="btn shop-now">
                            <a href="#" class="btn btn-default">Shop Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <img src=""/>
                        <div class="title">
                            <h1><strong>FULL HD TV</strong></h1>
                        </div>
                        <div class="description">
                            <span>Superior colors, brightness level, and slim design</span>
                        </div>        
                        <div class="btn shop-now">
                            <a href="#" class="btn btn-default">Shop Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <img src=""/>
                        <div class="title">
                            <h1><strong>UHD TV</strong></h1>
                        </div>
                        <div class="description">
                            <span>Innovative design with sophisticated 4K UHD technology</span>
                        </div>        
                        <div class="btn shop-now">
                            <a href="#" class="btn btn-default">Shop Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "category_top_section" => [
                'title' => 'Category Top Section',
                'content' => '<div class="category-top-section fixed-width">{{widget type="Kemana\TopCategories\Block\Widget\CategoryWidget" number_of_categories_to_display="8"}}</div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "home_page_blog_and_news" => [
                'title' => 'Blog & News Home Page',
                'content' => '{{widget type="Kemana\Blog\Block\Widget\Posts" post_count="3" show_type="new" template="Kemana_Blog::widget/posts.phtml"}}',
                'is_active' => 1,
                'store_id' => [HelperData::PG_STORE_VIEW_INDONESIA, HelperData::PG_STORE_VIEW_ENGLISH]
            ],
            "footer-social-icon-section" => [
                'title' => 'Home Footer Social Icon',
                'content' => '<div class="item">
	<ul class="link-social-network">
		<li class="facebook"><a href="https://www.facebook.com/www.planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
		<li class="tiktok"><a href="https://www.tiktok.com/@planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
		<li class="youtube"><a href="https://www.youtube.com/channel/UCt8enW66YkFkXt_3fbKrkmg" target="_blank" rel="noopener">&nbsp;</a></li>
		<li class="instagram"><a href="https://www.instagram.com/planetgadget.store/" target="_blank" rel="noopener">&nbsp;</a></li>
		<li class="whatsapp"><a href="https://wa.me/628113988888" target="_blank" rel="noopener">&nbsp;</a></li>
	</ul>
</div>',
                'is_active' => 1,
                'store_id' => [HelperData::PG_STORE_VIEW_INDONESIA, HelperData::PG_STORE_VIEW_ENGLISH]
            ],
            "header_top_line_help" => [
                'title' => 'Header Top Line Help',
                'content' => '<div class="heading">Contact Us</div>
<div class="text phone">{{config path="general/store_information/phone"}}</div>
<div class="text email">{{config path="trans_email/ident_general/email"}}</div>
<div class="text live-chat"><a href="https://pg.kemana.dev/product.html" target="_blank" rel="noopener">Live Chat</a></div>
<div class="time">Operational Hours: <span>09:00 - 21:00</span></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "main_banner_bottom_message" => [
                'title' => 'Home Page Main Banner Bottom Message',
                'content' => '<div class="pg-club-banner-desktop"><img src="{{media url=wysiwyg/home/PG-Club-Desktop.png}}" alt="" /></div>
<div class="pg-club-banner-mobile"><img src="{{media url=wysiwyg/home/PG-Club-Mobile.gif}}" alt="" /></div>',
                'is_active' => 1,
                'store_id' => 0
            ],
            "footer-address-section" => [
                'title' => 'ID - Footer Address Section',
                'content' => '<div class="logo"><img src="{{media url=wysiwyg/footer/Planet_Gadget_Footer_2x.png}}" alt=""></div>
<div class="customer-service">Silakan hubungi kami melalui<br>kontak informasi dibawah ini:</div>
<div class="customer-service-info">Jam Operasional: <span class="hours">09:00 - 21:00</span></div>
<div class="email"><a href="mailto:cs@planetgadget.store">{{config path="trans_email/ident_general/email"}}</a></div>
<div class="number"><a href="tel:{{config path="general/store_information/phone"}}">{{config path="general/store_information/phone"}}</a></div>
<div class="live-chat"><a href="https://wa.me/628113988888">Chat dengan kami!</a></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "footer-icons-section" => [
                'title' => 'ID - Footer Icons Section',
                'content' => '<div class="item payment delivery">
	<p class="title">Metode Pengiriman</p>
	<ul class="list payment-partners">
		<li class="gosend"><img src="{{media url=wysiwyg/footer/logos/gosend_2x.png}}" alt="" /></li>
		<li class="grab"><img src="{{media url=wysiwyg/footer/logos/grab_2x.png}}" alt="" /></li>
		<li class="jne"><img src="{{media url=wysiwyg/footer/logos/jne_2x.png}}" alt="" /></li>
		<li class="jnt"><img src="{{media url=wysiwyg/footer/logos/jnt_2x.png}}" alt="" /></li>
		<li class="sicepat"><img src="{{media url=wysiwyg/footer/logos/sicepat_2x.png}}" alt="" /></li>
		<li class="anteraja"><img src="{{media url=wysiwyg/footer/logos/anteraja_2x.png}}" alt="" /></li>
		<li class="ninja-express"><img src="{{media url=wysiwyg/footer/logos/ninjaexpress_2x.png}}" alt="" /></li>
		<li class="home-delivery"><img src="{{media url=wysiwyg/footer/logos/homedelivery_2x.png}}" alt="" /></li>
	</ul>
</div>
<div class="item payment">
	<p class="title">Metode Pembayaran</p>
	<ul class="link-trademark">
		<li class="mandiri"><img src="{{media url=wysiwyg/footer/logos/mandiri_2x.png}}" alt="" /></li>
		<li class="bca"><img src="{{media url=wysiwyg/footer/logos/bca_2x.png}}" alt="" /></li>
		<li class="bni"><img src="{{media url=wysiwyg/footer/logos/bni_2x.png}}" alt="" /></li>
		<li class="bri"><img src="{{media url=wysiwyg/footer/logos/bri_2x.png}}" alt="" /></li>
		<li class="cimb"><img src="{{media url=wysiwyg/footer/logos/cimb_2x.png}}" alt="" /></li>
		<li class="permata"><img src="{{media url=wysiwyg/footer/logos/permata_2x.png}}" alt="" /></li>
		<li class="gopay"><img src="{{media url=wysiwyg/footer/logos/gopay_2x.png}}" alt="" /></li>
		<li class="shopeepay"><img src="{{media url=wysiwyg/footer/logos/shopeepay_2x.png}}" alt="" /></li>
		<li class="akulaku"><img src="{{media url=wysiwyg/footer/logos/akulaku_2x.png}}" alt="" /></li>
		<li class="kredivo"><img src="{{media url=wysiwyg/footer/logos/kredivo_2x.png}}" alt="" /></li>
		<li class="home-credit"><img src="{{media url=wysiwyg/footer/logos/home_credit_2x.png}}" alt="" /></li>
		<li class="visa"><img src="{{media url=wysiwyg/footer/logos/visa_2x.png}}" alt="" /></li>
		<li class="mastercard"><img src="{{media url=wysiwyg/footer/logos/mastercard_2x.png}}" alt="" /></li>
		<li class="jcb"><img src="{{media url=wysiwyg/footer/logos/jcb_2x.png}}" alt="" /></li>
		<li class="indodana"><img src="{{media url=wysiwyg/footer/logos/indodana_2x.png}}" alt="" /></li>
		<li class="indomaret"><img src="{{media url=wysiwyg/footer/logos/indomaret_2x.png}}" alt="" /></li>
		<li class="alfamart"><img src="{{media url=wysiwyg/footer/logos/alfamart_2x.png}}" alt="" /></li>
		<li class="dandan"><img src="{{media url=wysiwyg/footer/logos/dandan_2x.png}}" alt="" /></li>
	</ul>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "home-page-new-arrivals" => [
                'title' => 'ID - Home Page New Arrivals',
                'content' => '<div class="home-new-arrivals-wrapper"><div class="home-new-arrivals-container fixed-width">{{widget type="Kemana\AcceleratorBase\Block\Catalog\Product\ProductsList" title="Gadget <span>Terbaru</span>" widget_type="newproducts" collection_sort_by="name" collection_sort_order="asc" show_pager="0" products_count="10" template="Kemana_AcceleratorBase::product/widget/content/grid.phtml" products_per_row="4" visible_products="4" visible_products_tablet="3" visible_products_mobile="1" pagination="false" widget_id="9745" conditions_encoded="^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^]^]"}}</div></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "recommended_from_seller_home_page" => [
                'title' => 'ID - Recommended From Seller Home Page',
                'content' => '<div class="home-editors-recommend-wrapper"><div class="home-editors-recommend-container fixed-width">{{widget type="Kemana\AcceleratorBase\Block\Catalog\Product\ProductsList" title="Rekomendasi <span>Editor</span>" widget_type="customproducts" collection_sort_by="name" collection_sort_order="asc" show_pager="0" products_count="6" template="Kemana_AcceleratorBase::product/widget/content/grid.phtml" products_per_row="6" visible_products="6" visible_products_tablet="3" visible_products_mobile="1" pagination="false" widget_id="6039" conditions_encoded="^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`sku`,`operator`:`()`,`value`:`SAMSUNG Z FOLD 4, SAMSUNG GALAXY A13, XI-70061, XIAOMI POCO C40, AS-60036-BL, SS-0258`^]^]"}}</div></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "header_top_line_help" => [
                'title' => 'ID - Header Top Line Help',
                'content' => '<div class="heading">Hubungi Kami</div>
<div class="text phone">{{config path="general/store_information/phone"}}</div>
<div class="text email">{{config path="trans_email/ident_general/email"}}</div>
<div class="text live-chat">Live Chat</div>
<div class="time">Jam Operasional: <span>09:00 - 21:00</span></div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "home-page-how-it-works" => [
                'title' => 'ID - Home Page How It Works',
                'content' => '<div class="how-it-works fixed-width">
	<div class="row">
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Garansi_Resmi_2x.png}}" alt="" /></span>
		    <h5>Garansi Resmi</h5>
		    <p>Semua produk bergaransi resmi / TAM</p>
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Benefit_Berbelanja_2x.png}}" alt="" /></span>
		    <h5>Dapatkan Benefit Berbelanja</h5>
		    <p>Beragam Promo dan Info Gadget Terbaru</p>
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Pengiriman_Terpercaya_2x.png}}" alt="" /></span>
		    <h5>Pengiriman Terpercaya</h5>
		    <p>Kirim Cepat Aman dan Terpercaya</p>
		</div>
	    </div>
	    <div class="col-md-3 col-6 text-block">
		<div class="how-it-works-item">
		    <span class="icon"><img src="{{media url=wysiwyg/footer/Customer_Service_2x.png}}" alt="" /></span>
		    <h5>Customer Service</h5>
		    <p>Kami siap menjawab semua pertanyaan Anda</p>
		</div>
	    </div>
	</div>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "home-page-inner-banner" => [
                'title' => 'ID - Home Page Inner Banner',
                'content' => '<div class="home-banner-first-container">
    <div class="banner-inner-container">
                                <div class="row">
                                    <div class="col-md-6 col-12 banner-block">
                                        <div class="banner-img"><a href="https://pg.kemana.dev/xiaomi-mi-11t-pro-5g-garansi-resmi-xiaomi.html/" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/mi-11t-pro_1_.jpg}}" alt="" /></a></div>
                                    </div>
                                    <div class="col-md-6 col-12 banner-block">
                                        <div class="banner-img"><a href="https://pg.kemana.dev/apple-iphone-13-garansi-resmi-apple.html/" target="_blank" rel="noopener"><img src="{{media url=wysiwyg/11655-iphone-13-indonesia_1_.jpg}}" alt="" /></a></div>
                                    </div>
                                </div>
    </div>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ],
            "kemana_contact_info_block" => [
                'title' => 'Contact Us Info Address Block',
                'content' => '<div class="block-title"><h4>PT Semua Karena Anugerah</h4></div>
<p>Please contact us via the contact information below:</p>
<div class="operating-hours"><strong>Operating Hours: </strong>{{config path="general/store_information/hours"}}</div>
<div class="contact-email"><strong><a href="mailto:{{config path="trans_email/ident_general/email"}}">{{config path="trans_email/ident_general/email"}}</a></strong></div>
<div class="contact-phone"><strong><a href="tel:{{config path="general/store_information/phone"}}">{{config path="general/store_information/phone"}}</a></strong></div>
<div class="contact-livechat"><strong><a href="https://wa.me/628113988888">Live Chat</a></strong></div>
</br>
<div class="contact-socialmedia-link">
<p>Follow us on social media</p>
<ul class="link-social-network">
<li class="facebook"><a href="https://www.facebook.com/www.planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="tiktok"><a href="https://www.tiktok.com/@planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="youtube"><a href="https://www.youtube.com/channel/UCt8enW66YkFkXt_3fbKrkmg" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="instagram"><a href="https://www.instagram.com/planetgadget.store/" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="whatsapp"><a href="https://wa.me/628113988888" target="_blank" rel="noopener">&nbsp;</a></li>
</ul>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "header_top_line_notice" => [
                'title' => 'Header Top Notice',
                'content' => '<p class="top-notice">NOTICE: Deliveries are applicable only for relevant areas</p>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "header_top_line_message" => [
                'title' => 'Header top line message',
                'content' => '<p class="top-promotion">Free Delivery for purchase above $10.00</p>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "footer-help-section" => [
                'title' => 'Footer Help Section',
                'content' => '<p class="title" data-role="collapsible">Help</p>
                                <ul class="list">
                                    <li>
                                        <a href="{{store url=\'faq\'}}">FAQ</a>
                                    </li>
                                    <li>
                                        <a href="{{store url=\'how-to-order\'}}">How to Order</a>
                                    </li>
                                    <li>
                                        <a href="{{store url=\'how-to-pay\'}}">How to Pay</a>
                                    </li>
                                    <li>
                                        <a href="{{store url=\'shipping-information\'}}">Shipping Information</a>
                                    </li>
                                    <li>
                                        <a href="{{store url=\'return-policy\'}}">Return Policy</a>
                                    </li>
                                    <li>
                                        <a href="{{store url=\'warranty-policy\'}}">Warranty Policy</a>
                                    </li>
                                    <li>
                                        <a href="#">Track Order</a>
                                    </li>
                                    <li>
                                        <a href="#">Bank Promo</a>
                                    </li>
                                </ul>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_ENGLISH
            ],
            "kemana_contact_info_block" => [
                'title' => 'ID - Contact Us Info Address Block',
                'content' => '<div class="block-title"><h4>PT Semua Karena Anugerah</h4></div>
<p>Silakan hubungi kami melalui kontak Informasi dibawah ini:</p>
<div class="operating-hours"><strong>Jam Operasional: </strong>{{config path="general/store_information/hours"}}</div>
<div class="contact-email"><strong><a href="mailto:{{config path="trans_email/ident_general/email"}}">{{config path="trans_email/ident_general/email"}}</a></strong></div>
<div class="contact-phone"><strong><a href="tel:{{config path="general/store_information/phone"}}">{{config path="general/store_information/phone"}}</a></strong></div>
<div class="contact-livechat"><strong><a href="https://wa.me/628113988888">Live Chat</a></strong></div>
</br>
<div class="contact-socialmedia-link">
<p>Ikuti kami di media sosial</p>
<ul class="link-social-network">
<li class="facebook"><a href="https://www.facebook.com/www.planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="tiktok"><a href="https://www.tiktok.com/@planetgadget.store" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="youtube"><a href="https://www.youtube.com/channel/UCt8enW66YkFkXt_3fbKrkmg" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="instagram"><a href="https://www.instagram.com/planetgadget.store/" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="whatsapp"><a href="https://wa.me/628113988888" target="_blank" rel="noopener">&nbsp;</a></li>
</ul>
</div>',
                'is_active' => 1,
                'store_id' => HelperData::PG_STORE_VIEW_INDONESIA
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
