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

namespace Kemana\Blog\Block\MonthlyArchive;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Kemana\Blog\Block\Frontend;
use Kemana\Blog\Helper\Data as DataHelper;

/**
 * Class Widget
 * @package Kemana\Blog\Block\MonthlyArchive
 */
class Widget extends Frontend
{
    /**
     * @var array
     */
    protected $_postDate;

    /**
     * @return mixed
     */
    public function isEnable()
    {
        return $this->helperData->getBlogConfig('monthly_archive/enable_monthly');
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDateArrayCount()
    {
        return array_values(array_count_values($this->getDateArray()));
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDateArrayUnique()
    {
        return array_values(array_unique($this->getDateArray()));
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getDateArray()
    {
        $dateArray = [];
        foreach ($this->getPostDate() as $postDate) {
            $dateArray[] = date("F Y", $this->dateTime->timestamp($postDate));
        }

        return $dateArray;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getPostDate()
    {
        if (!$this->_postDate) {
            $posts = $this->helperData->getPostList();
            $postDates = [];
            if ($posts->getSize()) {
                foreach ($posts as $post) {
                    $postDates[] = $post->getPublishDate();
                }
            }
            $this->_postDate = $postDates;
        }

        return $this->_postDate;
    }

    /**
     * @return int|void
     * @throws NoSuchEntityException
     */
    public function getDateCount()
    {
        $limit = $this->helperData->getBlogConfig('monthly_archive/number_records') ?: 5;
        $dateArrayCount = $this->getDateArrayCount();
        $count = count($dateArrayCount);

        return ($count < $limit) ? $count : $limit;
    }

    /**
     * @param $month
     *
     * @return string
     */
    public function getMonthlyUrl($month)
    {
        return $this->helperData->getBlogUrl($month, DataHelper::TYPE_MONTHLY);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDateLabel()
    {
        $postDates = $this->getPostDate();
        $postDatesLabel = [];
        if (count($postDates)) {
            foreach ($postDates as $date) {
                $postDatesLabel[] = $this->helperData->getDateFormat($date, true);
            }
        }

        return array_values(array_unique($postDatesLabel));
    }
}
