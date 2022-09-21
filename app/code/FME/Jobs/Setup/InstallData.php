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
namespace FME\Jobs\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{   
    public function __construct(\FME\Jobs\Model\Job $jobMetaType){
        $this->jobMetaType = $jobMetaType;
    }
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,       
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {    
        $contextInstall = $context;
        $contextInstall->getVersion();        
        $data = [];
        $statuses = [
            'location' => __('Location'),
            'department' => __('Department'),
            'job_type' => __('Job Type'),
            'gender' => __('Gender'),
            'job_positions' => __('No. of Positions'),
            'career_level' => __('Career Level'),
            'req_qualification' => __('Required Qualification'),
            'req_experience' => __('Required Experience'),                       
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $setup->getConnection()->insertArray($setup->getTable('fme_meta_type'), ['type_code', 'type_name'], $data);
    }
}
