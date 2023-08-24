<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Blog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   kemana team <jakartateam@kemana.com>
 */

namespace Kemana\Blog\Block;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Sitemap
 * @package Kemana\Blog\Block
 */
class Sitemap extends Frontend
{
    /**
     * @return $this|void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('sitemap', [
                'label' => __('Site Map'),
                'title' => __('Site Map')
            ]);
        }
    }

    /**
     * @param bool $meta
     *
     * @return array
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);

        if ($meta) {
            $blogTitle[] = __('Site Map');
        } else {
            $blogTitle = __('Site Map');
        }

        return $blogTitle;
    }
}
