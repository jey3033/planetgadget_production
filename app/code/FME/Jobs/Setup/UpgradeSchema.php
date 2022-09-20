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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.4', '<=')) {

            $setup->getConnection()->modifyColumn(
                    $setup->getTable('fme_jobs'),
                    'jobs_publish_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                        'comment' => 'Jobs Publish Date',
                    ]
            );
            $setup->getConnection()->modifyColumn(
                    $setup->getTable('fme_jobs'),
                    'jobs_applyby_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                        'comment' => 'Jobs Apply By Date',
                    ]
            );
            // Check if the table already exists
            $tableName='fme_jobs_application';
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'is_archived' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Is Archived',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        if (version_compare($context->getVersion(), '1.1.1', '<=')) {
            $tableName='fme_jobs_application';
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'is_archived' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Is Archived',
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            // Check if the table already exists
            $setup->getConnection()->addColumn(
                    $setup->getTable('fme_jobs'),
                    'use_config_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default' => 1,
                        'comment' => 'use config email',
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('fme_jobs'),
                    'notification_email_receiver',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'Notification Email Receiver',
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('fme_jobs'),
                    'use_config_template',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default' => 1,
                        'comment' => 'use config Template',
                    ]
            );
            $setup->getConnection()->addColumn(
                    $setup->getTable('fme_jobs'),
                    'email_notification_temp',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'email notification template',
                    ]
            );
        }
        $setup->endSetup();
    }
}

