<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Template
 * @package Kemana\Banner\Model\Config\Source
 */
class Template implements OptionSourceInterface
{
    /**
     * Const for Demo 1
     */
    const DEMO1 = 'demo1.jpg';

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Json
     */
    protected $json;

    /**
     * Template constructor.
     * @param Repository $assetRepo
     * @param Json $json
     */
    public function __construct(
        Repository $assetRepo,
        Json $json
    ) {
        $this->json = $json;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DEMO1,
                'label' => __('Advance template 1')
            ]
        ];
    }

    /**
     * @return false|string
     */
    public function getTemplateHtml()
    {
        $imgTmp = '<div class="full-width-banner main-home-slider">
    <div class="banner-img">
        <img class="desktop" src="{{media url="wysiwyg/main-banner-slider.png"}}" alt="">
        <img class="mobile" src="{{media url="wysiwyg/main-banner-slider-m.png"}}" alt="">
    </div>
    <div class="banner-description color-white center">
        <div class="banner-description-container">
             <h1><-- Banner Title goes here --></h1>
             <p><-- Banner description goes here --></p>
             <div class="actions-toolbar">
                  <div class="primary">
                       <a class="action primary shop-now" href="#">SHOP NOW</a>
                  </div>
             </div>
        </div>
    </div>
</div>';
        $templates = [
            self::DEMO1 => [
                'tpl' => $imgTmp,
                'var' => '{{imgName}}'
            ]
        ];

        return $this->json->serialize($templates);
    }

    /**
     * @return false|string
     */
    public function getImageUrls()
    {
        $urls = [];
        foreach ($this->toOptionArray() as $template) {
            $urls[$template['value']] = $this->assetRepo->getUrl('Kemana_Banner::images/' . $template['value']);
        }

        return $this->json->serialize($urls);
    }
}
