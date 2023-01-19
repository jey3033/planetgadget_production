<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_InventorySyncCron
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Tushar Korat <tushar@kemana.com>
 */
 
namespace Kemana\InventorySyncCron\Cron;

class InventorySync
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Kemana\InventorySyncCron\Helper\Data
     */
    protected $helperData;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Kemana\InventorySyncCron\Helper\Data $helperData
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
    }

	public function execute()
	{
		$collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        foreach ($collection as $product) {
            $this->helperData->updateProductStock($product->getId());
        }
		return $this;
	}
}