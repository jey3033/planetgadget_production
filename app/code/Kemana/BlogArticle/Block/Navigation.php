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

use Magento\Framework\View\Element\Html\Links;

/**
 * Class Navigation
 * @package Kemana\Blog\Block
 */
class Navigation extends Links
{
    /**
     * {@inheritdoc}
     */
    public function getLinks()
    {
        $links = parent::getLinks();

        usort($links, [$this, "compare"]);

        return $links;
    }

    /**
     * @param $firstLink
     * @param $secondLink
     *
     * @return bool
     */
    private function compare($firstLink, $secondLink)
    {
        return ($firstLink->getData('sortOrder') > $secondLink->getData('sortOrder'));
    }
}
