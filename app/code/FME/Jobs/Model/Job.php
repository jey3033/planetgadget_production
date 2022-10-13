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
namespace FME\Jobs\Model;

class Job extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    protected $_logger;

    protected function _construct()
    {
        $this->_init('FME\Jobs\Model\ResourceModel\Job');
    }

    public function getAvailableStatuses()
    {
        $availableOptions = ['0' => 'Disable',
                           '1' => 'Enable'];
        return $availableOptions;
    }

    public function getTypes()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_type'), ['id','type_name','type_code']);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getTypesCode($cid)
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['type_code'])
        ->where('data_code = ?', $cid);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getCvDownloadLink($cid)
    {

        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_jobs_application'), ['cvfile'])
        ->where('app_id = ?', $cid);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getApplicantsCount($cid)
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_jobs_application'))
        ->where('jobs_id = ?', $cid);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        $totalApplicants = count($data);
        return $totalApplicants;
    }

    public function getDepartments()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 4);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getLocations()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 1);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getTypesOpt()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 7);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getGender()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 10);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getCareer()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 16);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getExperience()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 22);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }
    public function getPositions()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 13);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    public function getQualifications()
    {
        $select = $this->_getResource()->getConnection()->select()->from($this->_getResource()->getTable('fme_meta_data'), ['data_code','type_code','data_name'])
        ->where('type_code = ?', 19);
        $data = $this->_getResource()->getConnection()
          ->fetchAll($select);
        return $data;
    }

    Public function getMetTypeForInstallScript()
    {
        $collection = $this->_getResource()->getConnection()->select()->from('fme_meta_type');
         $data = $this->_getResource()->getConnection()
          ->fetchAll($collection);
        return $data;
    }
}
