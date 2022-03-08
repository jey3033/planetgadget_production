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
namespace FME\Jobs\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\EntityManager;

class Job extends AbstractDb
{
    protected $_store = null;
    protected $_storeManager;
    protected $entityManager;
    protected $metadataPool;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('fme_jobs', 'jobs_id');
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $data= $object->getData('store_id');
        $data=(array) $data;
        if(!empty($data)){
            foreach($data as $dat)
            { 
                $store[]=["jobs_id" => $object->getId(),
                    "store_id" => $dat];
            }
            if(!empty($store)){
                $where = ['jobs_id = ?' => (int)$object->getId(), 'jobs_id IN (?)' => $object->getId()];
                $this->getConnection()->delete('fme_jobs_store', $where);
                $this->getConnection()->insertMultiple('fme_jobs_store', $store);
            }
        }
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId=$this->lookupStore($object->getId());
        $object->setData('store_id',$storeId);
    }
    
    public function lookupStore($jobid)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('fme_jobs_store'),
            'store_id'
        )->where(
            'jobs_id = ?',
            (int)$jobid
        );
        return $connection->fetchCol($select);
    }
}
