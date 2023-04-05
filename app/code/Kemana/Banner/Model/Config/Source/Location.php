<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Location
 * @package Kemana\Banner\Model\Config\Source
 */
class Location implements OptionSourceInterface
{
    /**
     * Const Allpage Content Top
     */
    const ALLPAGE_CONTENT_TOP = 'allpage.content-top';

    /**
     * Const Allpage Content Bottom
     */
    const ALLPAGE_CONTENT_BOTTOM = 'allpage.content-bottom';

    /**
     * Const Allpage Page Top
     */
    const ALLPAGE_PAGE_TOP = 'allpage.page-top';

    /**
     * Const Allpage page Bottom
     */
    const ALLPAGE_PAGE_BOTTOM = 'allpage.footer-container';

    /**
     * Const Homepage Content Top
     */
    const HOMEPAGE_CONTENT_TOP = 'cms_index_index.content-top';

    /**
     * Const Homepage Content Bottom
     */
    const HOMEPAGE_CONTENT_BOTTOM = 'cms_index_index.content-bottom';

    /**
     * Const Homepage Page Top
     */
    const HOMEPAGE_PAGE_TOP = 'cms_index_index.page-top';

    /**
     * Const Homepage Page Bottom
     */
    const HOMEPAGE_PAGE_BOTTOM = 'cms_index_index.footer-container';

    /**
     * Const Category Content top
     */
    const CATEGORY_CONTENT_TOP = 'catalog_category_view.content-top';

    /**
     * Const Category Content Bottom
     */
    const CATEGORY_CONTENT_BOTTOM = 'catalog_category_view.content-bottom';

    /**
     * Const Category Page Top
     */
    const CATEGORY_PAGE_TOP = 'catalog_category_view.page-top';

    /**
     * Const Category Page Bottom
     */
    const CATEGORY_PAGE_BOTTOM = 'catalog_category_view.footer-container';

    /**
     * Const Category Sidebar Top
     */
    const CATEGORY_SIDEBAR_TOP = 'catalog_category_view.sidebar-top';

    /**
     * Const Category Sidebar Bottom
     */
    const CATEGORY_SIDEBAR_BOTTOM = 'catalog_category_view.sidebar-bottom';

    /**
     * Const Product Content Top
     */
    const PRODUCT_CONTENT_TOP = 'catalog_product_view.content-top';

    /**
     * Const Product Content Bottom
     */
    const PRODUCT_CONTENT_BOTTOM = 'catalog_product_view.content-bottom';

    /**
     * Const Product Page Top
     */
    const PRODUCT_PAGE_TOP = 'catalog_product_view.page-top';

    /**
     * Const Product Page Bottom
     */
    const PRODUCT_PAGE_BOTTOM = 'catalog_product_view.footer-container';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Home Page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::HOMEPAGE_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::HOMEPAGE_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::HOMEPAGE_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::HOMEPAGE_PAGE_BOTTOM
                    ]
                ]
            ],
            [
                'label' => __('Category page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::CATEGORY_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::CATEGORY_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::CATEGORY_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::CATEGORY_PAGE_BOTTOM
                    ],
                    [
                        'label' => __('Top of sidebar'),
                        'value' => self::CATEGORY_SIDEBAR_TOP
                    ],
                    [
                        'label' => __('Bottom of sidebar'),
                        'value' => self::CATEGORY_SIDEBAR_BOTTOM
                    ],
                ]
            ],
            [
                'label' => __('Product page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::PRODUCT_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::PRODUCT_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::PRODUCT_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::PRODUCT_PAGE_BOTTOM
                    ]
                ]
            ]
        ];

        return $options;
    }
}
