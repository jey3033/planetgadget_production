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
namespace FME\Jobs\Model\ResourceModel\Job;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'jobs_id';
    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('FME\Jobs\Model\Job', 'FME\Jobs\Model\ResourceModel\Job');
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
    public function addStoreViewFilter($store_id) {
        $limit=1;
        $this->getSelect()->join(
        ['fme_stor' => $this->getTable('fme_jobs_store')], 'main_table.jobs_id = fme_stor.jobs_id', []
        )->where('fme_stor.store_id in (?)', [0, $store_id])->group('main_table.jobs_id');
        return $this;
    }

    protected function _afterLoad()
    {
        $this->performAfterLoad('fme_jobs_store', 'jobs_id');
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('fme_jobs_store', 'jobs_id');
    }
}
