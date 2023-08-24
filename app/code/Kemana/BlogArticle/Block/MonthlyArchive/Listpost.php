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

use Kemana\Blog\Helper\Data;
use Kemana\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class Listpost
 * @package Kemana\Blog\Block\MonthlyArchive
 */
class Listpost extends \Kemana\Blog\Block\Listpost
{
    /**
     * Override this function to apply collection for each type
     *
     * @return Collection
     */
    protected function getCollection()
    {
        return $this->helperData->getPostCollection(Data::TYPE_MONTHLY, $this->getMonthKey());
    }

    /**
     * @return mixed
     */
    protected function getMonthKey()
    {
        $monthKey   = $this->getRequest()->getParam('month_key');
        $monthParts = explode('-', $monthKey);
        $monthVal   = '01';
        if (!empty($monthParts[1])) {
            $checkMonth = (int) $monthParts[1];
            if ($checkMonth > 0 && $checkMonth <= 12) {
                $monthVal = $monthParts[1];
            }
        }
        $monthKey = $monthParts[0] . '-' . $monthVal;
        return $monthKey;
    }

    /**
     * @return false|string
     */
    protected function getMonthLabel()
    {
        return $this->helperData->getDateFormat($this->getMonthKey() . '-10', true);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb($this->getMonthKey(), [
                'label' => __('Monthy Archive'),
                'title' => __('Monthy Archive'),
            ]);
        }
    }

    /**
     * @param bool $meta
     *
     * @return array|false|string
     */
    public function getBlogTitle($meta = false)
    {
        $blogTitle = parent::getBlogTitle($meta);

        if ($meta) {
            array_push($blogTitle, $this->getMonthLabel());

            return $blogTitle;
        }

        return $this->getMonthLabel();
    }
}
