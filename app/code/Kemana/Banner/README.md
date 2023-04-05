# Banner
@version     2.0.5, last modified July 2020
@license     https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
@copyright   Copyright © 2020 Kemana. All rights reserved.
@link        http://www.kemana.com Driving Digital Commerce

#Features
01. Can Upload Image Banners Or HTML content as banners
02. Can create sliders
03. Sliders can be scheduled to display or remove on given time
04. cron job will handle the slider schedule time and cache cleaning for relevant slider activation time
05. Sliders can be assigned as widget to any page.

##Release notes
2.0.1 - initial commit

2.0.2 - fixes for system xml file path issue

2.0.3 - single banner slider without javascript

2.0.4 - support with mobile banner image

2.0.5 - fixes for save without from date, next and prev buttons UI fixes

2.0.6 - fixes on Home page

2.41.0 - Upgrade to Magento 2.4.1 with Modifications

2.42.0 - Upgrade to Magento 2.4.2 with Modifications

2.43.0 - Upgrade to Magento 2.4.3 with Modifications

2.43.1 - Code improvements

##Notes
* Add Widget with name "Banner Slider widget" and set "Slider Id" for it.
  CMS Page/Static Block
  {{block class="Kemana\Banner\Block\Widget" slider_id="1"}}
  You can paste the above block of snippet into any page in Magento 2 and set SliderId for it.
  
  Template .phtml file
  <?= $block->getLayout()->createBlock("Kemana\Banner\Block\Widget::class")->setSliderId(1)->toHtml();?>
  Open a .phtml file and insert where you want to display Banner Slider.
  
* If single banner is assigned to a Slider, then no javascript will be used to show, it will just show the image or html content as it is.