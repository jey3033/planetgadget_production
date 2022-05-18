<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_StockAvailabilityPopup
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\StockAvailabilityPopup\Model\Stock;

use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

/**
 * Class Source
 */
class SourceDataForSku
{
    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $sourceItemsBySku;
    /**
     * @var SourceRepositoryInterface
     */

    private $sourceRepositoryInterface;

    /**
     * @param GetSourceItemsBySkuInterface $sourceItemsBySku
     * @param SourceRepositoryInterface $sourceRepositoryInterface
     */
    public function __construct(
        GetSourceItemsBySkuInterface $sourceItemsBySku,
        SourceRepositoryInterface    $sourceRepositoryInterface
    )
    {
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->sourceRepositoryInterface = $sourceRepositoryInterface;
    }

    /**
     * @param $sku
     * @return \Magento\InventoryApi\Api\Data\SourceItemInterface[]
     */
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }

    public function getSourceItemLocationData($sourceCode)
    {
        return $this->sourceRepositoryInterface->get($sourceCode);
    }

}
