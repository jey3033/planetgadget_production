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

namespace Kemana\Blog\Block\Topic;

use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data;

/**
 * Class Widget
 * @package Kemana\Blog\Block\Topic
 */
class Widget extends Frontend
{
    /**
     * @return array|string
     */
    public function getTopicList()
    {
        $collection = $this->helperData->getObjectList(Data::TYPE_TOPIC);

        return $collection;
    }

    /**
     * @param $topic
     *
     * @return string
     */
    public function getTopicUrl($topic)
    {
        return $this->helperData->getBlogUrl($topic, Data::TYPE_TOPIC);
    }
}
