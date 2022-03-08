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
namespace FME\Jobs\Model\ResourceModel\Mdata\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use FME\Jobs\Model\ResourceModel\Mdata\Collection as PageCollection;

class Collection extends PageCollection implements SearchResultInterface
{
    
    protected $aggregations;
    
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $metadataPool,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }
    protected function _initSelect()
    {
        parent::_initSelect();
 
        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('fme_meta_type')], //2nd table name by which you want to join mail table
            'main_table.type_code = secondTable.id', // common column which available in both table 
            '*' // '*' define that you want all column of 2nd table. if you want some particular column then you can define as ['column1','column2']
        );
    }
    
    public function getAggregations()
    {
        return $this->aggregations;
    }
    
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }
    
    public function getSearchCriteria()
    {
        return null;
    }
    
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }
    
    public function getTotalCount()
    {
        return $this->getSize();
    }
    
    public function setTotalCount($totalCount)
    {
        return $this;
    }
    
    public function setItems(array $items = null)
    {
        return $this;
    }
}
