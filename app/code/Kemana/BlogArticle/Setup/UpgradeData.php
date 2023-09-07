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

namespace Kemana\Blog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kemana\Blog\Model\AuthorFactory;
use Kemana\Blog\Model\CommentFactory;

/**
 * Class UpgradeData
 * @package Kemana\Blog\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Date model
     *
     * @var DateTime
     */
    public $date;

    /**
     * @var CommentFactory
     */
    public $comment;

    /**
     * @var AuthorFactory
     */
    public $author;

    /**
     * UpgradeData constructor.
     *
     * @param DateTime $date
     * @param CommentFactory $commentFactory
     * @param AuthorFactory $authorFactory
     */
    public function __construct(
        DateTime $date,
        CommentFactory $commentFactory,
        AuthorFactory $authorFactory
    ) {
        $this->comment = $commentFactory;
        $this->author = $authorFactory;
        $this->date = $date;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.4.4', '<')) {
            $commentIds = $this->comment->create()->getCollection()->getAllIds();
            $commentIds = implode(',', $commentIds);
            if ($commentIds) {
                /** Add create at old comment */
                $sampleTemplates = [
                    'created_at' => $this->date->date(),
                    'status' => 3
                ];
                $setup->getConnection()->update(
                    $setup->getTable('kemana_blog_comment'),
                    $sampleTemplates,
                    'comment_id IN (' . $commentIds . ')'
                );
            }
        }

        if (version_compare($context->getVersion(), '2.5.2', '<')) {
            if ($this->author->create()->getCollection()->count() < 1) {
                $this->author->create()->addData(
                    [
                        'name' => 'Admin',
                        'type' => 0,
                        'status' => 1,
                        'created_at' => $this->date->date()
                    ]
                )->save();
            }
        }
        $installer->endSetup();
    }
}
