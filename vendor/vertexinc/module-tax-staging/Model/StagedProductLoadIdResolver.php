<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\TaxStaging\Model;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Vertex\Tax\Model\ProductLoadIdResolverInterface;
use Vertex\TaxStaging\Model\ResourceModel\Commodity as CommodityResource;

class StagedProductLoadIdResolver implements ProductLoadIdResolverInterface
{
    /** @var CommodityResource $commodityResource */
    private $commodityResource;

    public function __construct(CommodityResource $commodityResource)
    {
        $this->commodityResource = $commodityResource;
    }

    /**
     * Get the product load Id
     *
     * @param ProductInterface $product
     * @return int
     * @throws Exception
     */
    public function execute(ProductInterface $product): int
    {
        $productId = $this->commodityResource->getProductRowIdByVersionId($product);
        if ($productId) {
            return (int) $productId;
        }

        if (method_exists($product, 'getData')) {
            return (int) $product->getData($this->commodityResource->getProductLinkField());
        }
        return (int) $product->getId();
    }
}
