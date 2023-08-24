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

namespace Kemana\Blog\Block\Sidebar;

use Magento\Framework\Exception\NoSuchEntityException;
use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data;

/**
 * Class Search
 * @package Kemana\Blog\Block\Sidebar
 */
class Search extends Frontend
{
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSearchBlogData()
    {
        $result = [];
        $posts = $this->helperData->getPostList();
        $limitDesc = (int)$this->getSidebarConfig('search/description');
        if (!empty($posts)) {
            foreach ($posts as $item) {
                $shortDescription = ($item->getShortDescription() && $limitDesc > 0) ?
                    $item->getShortDescription() : '';
                if (strlen($shortDescription) > $limitDesc) {
                    $shortDescription = mb_substr($shortDescription, 0, $limitDesc, 'UTF-8') . '...';
                }

                $result[] = [
                    'value' => $item->getName(),
                    'url' => $item->getUrl(),
                    'image' => $this->resizeImage($item->getImage(), '100x'),
                    'desc' => $shortDescription
                ];
            }
        }

        return $this->helperData->jsonEncode($result);
    }

    /**
     * get sidebar config
     *
     * @param $code
     * @param $storeId
     *
     * @return mixed
     */
    public function getSidebarConfig($code, $storeId = null)
    {
        return $this->helperData->getBlogConfig('sidebar/' . $code, $storeId);
    }
}
