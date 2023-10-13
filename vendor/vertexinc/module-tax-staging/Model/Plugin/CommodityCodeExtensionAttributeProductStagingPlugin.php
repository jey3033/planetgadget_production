<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

declare(strict_types=1);

namespace Vertex\TaxStaging\Model\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogStaging\Api\ProductStagingInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\ExceptionLogger;
use Vertex\Tax\Model\Repository\CommodityCodeProductRepository;
use Vertex\TaxStaging\Model\ResourceModel\Commodity as CommodityResource;

/**
 * Update commodity code extension attribute when scheduling a content staging change
 *
 * @see ProductStagingInterface
 */
class CommodityCodeExtensionAttributeProductStagingPlugin
{
    /** @var CommodityResource */
    private $commodityResource;

    /** @var Config */
    private $config;

    /** @var ExceptionLogger */
    private $logger;

    /** @var CommodityCodeProductRepository */
    private $repository;

    public function __construct(
        CommodityCodeProductRepository $repository,
        ExceptionLogger $logger,
        Config $config,
        CommodityResource $commodityResource
    ) {
        $this->repository = $repository;
        $this->logger = $logger;
        $this->config = $config;
        $this->commodityResource = $commodityResource;
    }

    /**
     * Save commodity code extension attribute after scheduling a content staging update
     *
     * @param ProductStagingInterface $subject
     * @param bool $result
     * @param ProductInterface $product
     * @param int $version
     * @param array $arguments
     * @return bool
     * @throws \Exception If entity type is not found.
     * @see ProductStagingInterface::schedule()
     */
    public function afterSchedule(
        ProductStagingInterface $subject,
        bool $result,
        ProductInterface $product,
        $version,
        $arguments = []
    ): bool {
        if (!$this->config->isVertexActive() || !$result || !method_exists($product, 'getData')) {
            return $result;
        }

        $commodityCodeData = $product->getData('vertex_commodity_code');
        $productId = $product->getData($this->commodityResource->getProductLinkField());

        if ($commodityCodeData) {
            $codeModel = $this->commodityResource->getCodeModel($productId);
            $codeModel->setCode($commodityCodeData['code']);
            $codeModel->setType($commodityCodeData['type']);

            try {
                $this->repository->save($codeModel);
                $this->commodityResource->setRollBackData($product, $version);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        } else {
            $this->commodityResource->deleteByProductId($productId);
        }

        return $result;
    }

    /**
     * Remove commodity code extension attribute after unscheduling a content staging update
     *
     * @param ProductStagingInterface $subject
     * @param bool $result
     * @param ProductInterface $product
     * @return bool
     * @throws \Exception
     * @see ProductStagingInterface::unschedule()
     */
    public function afterUnschedule(
        ProductStagingInterface $subject,
        bool $result,
        ProductInterface $product
    ): bool {
        if (!$result || !method_exists($product, 'getData')) {
            return $result;
        }

        $productId = $product->getData($this->commodityResource->getProductLinkField());
        $this->commodityResource->deleteByProductId($productId);

        return $result;
    }
}
