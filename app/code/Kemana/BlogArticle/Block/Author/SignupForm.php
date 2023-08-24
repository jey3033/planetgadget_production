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

namespace Kemana\Blog\Block\Author;

use Kemana\Blog\Block\Frontend;

/**
 * Class SignupForm
 * @package Kemana\Blog\Block\Author
 */
class SignupForm extends Frontend
{
    /**
     * @return mixed
     */
    public function getUrlSuffix()
    {
        return $this->helperData->getUrlSuffix();
    }

    /**
     * @return array
     */
    public function getAuthor()
    {
        $author = $this->coreRegistry->registry('mp_author');

        if ($author) {
            return [
                'name' => $author->getName(),
                'status' => $this->authorStatusType->toArray()[$author->getStatus()],
                'url_key' => $author->getUrlKey(),
                'short_description' => $author->getShortDescription(),
                'image' => $author->getImage(),
                'facebook_link' => $author->getFacebookLink(),
                'twitter_link' => $author->getTwitterLink(),
            ];
        }

        return [
            'name' => '',
            'url_key' => '',
            'short_description' => '',
            'image' => '',
            'facebook_link' => '',
            'twitter_link' => '',
        ];
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $array = explode('/', $this->helperData->getCmsWysiwygEditor());
        if ($array[count($array) - 1] === 'tinymce4Adapter') {
            return 4;
        }

        return 3;
    }

    /**
     * @return int
     */
    public function getMagentoVersion()
    {
        return (int)$this->helperData->versionCompare('2.3.0') ? 3 : 2;
    }
}
