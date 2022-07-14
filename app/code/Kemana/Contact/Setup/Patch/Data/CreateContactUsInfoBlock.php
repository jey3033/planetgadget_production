<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Contact
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Contact\Setup\Patch\Data;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Store\Model\Store;

/**
 * Class CreateContactUsInfoBlock
 * @package Kemana\Contact\Setup\Patch\Data
 */
class CreateContactUsInfoBlock implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var CollectionFactory
     */
    private $blockCollectionFactory;

    /**
     * CreateContactUsInfoBlock constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $blockCollectionFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory $blockCollectionFactory,
        BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $cmsBlockIdentifier = 'kemana_contact_info_block';
        $newCmsStaticBlock = [
            'title' => 'Contact Us Info Address Block',
            'identifier' => $cmsBlockIdentifier,
            'content' => '<div class="block-title"><h4>PT All Because of Grace</h4></div>
<p>Please contact us via the contact information below:</p>
<div class="operating-hours"><strong>Operating Hours: </strong>{{config path="general/store_information/hours"}}</div>
<div class="contact-email"><strong><a href="mailto:{{config path="trans_email/ident_general/email"}}">{{config path="trans_email/ident_general/email"}}</a></strong></div>
<div class="contact-phone"><strong><a href="tel:{{config path="general/store_information/phone"}}">{{config path="general/store_information/phone"}}</a></strong></div>
<div class="contact-livechat"><strong><a href="#">Live Chat</a></strong></div>
<div class="contact-socialmedia-link">
<p>Follow us on social media</p>
<ul class="link-social-network">
<li class="facebook"><a href="#" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="tiktok"><a href="#" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="youtube"><a href="#" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="instagram"><a href="#" target="_blank" rel="noopener">&nbsp;</a></li>
<li class="whatsapp"><a href="#" target="_blank" rel="noopener">&nbsp;</a></li>
</ul>
</div>',
            'is_active' => 1,
            'stores' => Store::DEFAULT_STORE_ID
        ];

        $collection = $this->blockCollectionFactory->create();
        $collection->addFieldToFilter(BlockInterface::IDENTIFIER, $cmsBlockIdentifier);
        $collection->walk('delete');

        $block = $this->blockFactory->create();
        $block->setData($newCmsStaticBlock)->save();
        $this->moduleDataSetup->endSetup();
    }

    /**
     *  Uninstall or remove all the data when related to this module
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $cmsBlockIdentifier = 'kemana_contact_info_block';
        $collection = $this->blockCollectionFactory->create();
        $collection->addFieldToFilter(BlockInterface::IDENTIFIER, $cmsBlockIdentifier);
        $collection->walk('delete');

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
