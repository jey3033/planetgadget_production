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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'fme_meta_type'
         */
         $tableName = $installer->getTable('fme_meta_type');
         if ($setup->getConnection()->isTableExists($tableName) == true)
            {
                $installer->getConnection()->dropTable($tableName);
            }
        $table = $installer->getConnection()->newTable(
            $installer->getTable('fme_meta_type')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Type ID'
        )->addColumn(
            'type_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type Name'
        )->addColumn(
            'type_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'primary' => true],
            'Type Code'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('fme_meta_type'),
                ['type_name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['type_name'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'FME Meta Type Table'
        );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'fme_meta_data'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('fme_meta_data')
        )->addColumn(
            'data_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Data Code'
        )->addColumn(
            'data_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Data Name'
        )->addColumn(
            'data_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Data Description'
        )->addColumn(
            'data_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Data Status'
        )->addColumn(
            'type_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Type Code'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('fme_meta_data'),
                ['data_name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['data_name'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'FME Metadata Table'
        )->addForeignKey(
                $installer->getFkName('fme_meta_data', 'type_code', 'fme_meta_type', 'id'),
                'type_code',
                $installer->getTable('fme_meta_type'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
        /**
         * Create table 'fme_jobs'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('fme_jobs')
        )->addColumn(
            'jobs_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Jobs ID'
        )->addColumn(
            'jobs_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Job Name'
        )->addColumn(
            'jobs_url_key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Job Url Key'
        )->addColumn(
            'jobs_open_positions',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Jobs Open'
        )->addColumn(
            'jobs_select_departments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Jobs Department'
        )->addColumn(
            'jobs_job_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Jobs Type'
        )->addColumn(
            'jobs_location',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Jobs Locations'
        )->addColumn(
            'jobs_gender',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Jobs Gender'
        )->addColumn(
            'jobs_career_level',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Jobs Career Level'
        )->addColumn(
            'jobs_min_qualification',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Jobs Min Qualification'
        )->addColumn(
            'jobs_min_experience',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Jobs Min Experience'
        )->addColumn(
            'jobs_required_travel',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Jobs Required Travel'
        )->addColumn(
            'jobs_publish_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Jobs Publish Date'
        )->addColumn(
            'jobs_applyby_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Jobs Apply By Date'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Store ID'
        )->addColumn(
            'jobs_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Jobs Detail'
        )->addColumn(
            'jobs_required_skills',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Jobs Skill Set'
        )->addColumn(
            'job_page_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job Meta Title'
        )->addColumn(
            'job_meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Job Meta Keywords'
        )->addColumn(
            'job_meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Job Meta Description'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Job Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Jobs Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Jobs Active'
        )->addColumn(
            'use_config_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'use config email'
        )->addColumn(
            'notification_email_receiver',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Notification Email Receiver'
        )->addColumn(
            'use_config_template',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'use config template'
        )->addColumn(
            'email_notification_temp',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Notification Email template'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('fme_jobs'),
                ['jobs_title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['jobs_title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'FME Jobs Table'
        );

        $installer->getConnection()->createTable($table);

        // Added by Kemana

        /**
         * Add new column 'fme_jobs' table
         */
        if (version_compare($context->getVersion(), '1.1.7', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable('fme_jobs'),
                'salary_from',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Salary From',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('fme_jobs'),
                'salary_to',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Salary To',
                ]
            );
        }

        // End - Added by Kemana

        /**
         * Create table 'fme_jobs_application'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('fme_jobs_application')
        )->addColumn(
            'app_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Application ID'
        )->addColumn(
            'jobs_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Jobs ID'
        )->addColumn(
            'fullname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant Full Name'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant Email'
        )->addColumn(
            'dob',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant DoB'
        )->addColumn(
            'nationality',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant nationality'
        )->addColumn(
            'telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant telephone'
        )->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant Address'
        )->addColumn(
            'jobs_select_departments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Department'
        )->addColumn(
            'jobs_job_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job Type'
        )->addColumn(
            'jobs_location',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job Location'
        )->addColumn(
            'jobs_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Job Title'
        )->addColumn(
            'zipcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant Zipcode'
        )->addColumn(
            'cvfile',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Applicant Cv'
        )->addColumn(
            'comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Applicant Comments'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Applicant Creation Time'
        )->addColumn(
            'is_archived',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Archived'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Applicant Modification Time'
        )->addIndex(
            $installer->getIdxName('fme_jobs', ['jobs_id']),
            ['jobs_id']
        );
        $installer->getConnection()->createTable($table);

        // Added by Kemana
        /**
         * Add new column 'fme_jobs_application' table
         */
        if (version_compare($context->getVersion(), '1.1.7', '<')) {

            $setup->getConnection()->addColumn(
                $setup->getTable('fme_jobs_application'),
                'cover_letter_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Cover Letter File',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('fme_jobs_application'),
                'id_card_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'ID Card File',
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('fme_jobs_application'),
                'education_cert_file',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'ID Education Certificate File',
                ]
            );
        }

        // End - Added by Kemana
        /*
            Multisotre Table ..fme_jobs_store
        */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('fme_jobs_store')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'jobs_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Jobs ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            255,
            ['nullable' => false, 'primary' => true],
            'Store ID'
        );
        $installer->getConnection()->createTable($table);
    }
}
