<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_Jobs
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Jobs\Model\ResourceModel\Mdata;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'data_code';
    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('FME\Jobs\Model\Mdata', 'FME\Jobs\Model\ResourceModel\Mdata');
        $this->_map['fields']['jobs_id'] = 'main_table.jobs_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('fme_jobs_store', 'jobs_id');
        $this->_previewFlag = false;
        $joinTable = $this->getTable('fme_meta_type');
        $this->getSelect()-> join($joinTable.' as cpev','main_table.type_code = cpev.id',array('*'));
        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('fme_jobs_store', 'jobs_id');
        $joinTable = $this->getTable('fme_meta_type');
    }
}
