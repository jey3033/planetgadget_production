<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

declare(strict_types=1);

namespace Vertex\TaxStaging\Ui\DataProvider\Product\Form\Modifier;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ExceptionLogger;
use Vertex\Tax\Model\Repository\CommodityCodeProductRepository;
use Vertex\Tax\Ui\DataProvider\Product\Form\Modifier\CommodityCode as TaxCommodityCode;
use Vertex\Tax\Model\Config\Source\CommodityTypes;

/**
 * Adds "Vertex Commodity Code" input to Content Staging Product Form
 */
class CommodityCode extends TaxCommodityCode
{
    /** @var MetadataPool */
    private $metadataPool;

    public function __construct(
        Config $config,
        CommodityCodeProductRepository $repository,
        ExceptionLogger $logger,
        LocatorInterface $locator,
        MetadataPool $metadataPool,
        CommodityTypes $commodityTypes
    ) {
        parent::__construct($config, $repository, $logger, $locator, $commodityTypes);
        $this->metadataPool = $metadataPool;
    }

    /**
     * Return product link field Id
     *
     * @return string
     * @throws Exception
     */
    protected function getProductLoadId()
    {
        $product = $this->locator->getProduct();
        if (method_exists($product, 'getData')) {
            return $product->getData($this->getProductLinkField());
        }
    }

    /**
     * Get the product entity link field
     *
     * @return string
     * @throws Exception If entity type is not found.
     */
    private function getProductLinkField()
    {
        return $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }
}
