<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

declare(strict_types=1);

namespace Vertex\TaxStaging\Model\ResourceModel;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Vertex\Tax\Model\Config;
use Vertex\Tax\Model\Data\CommodityCodeProduct;
use Vertex\Tax\Model\Data\CommodityCodeProductFactory;
use Vertex\Tax\Model\ExceptionLogger;
use Vertex\Tax\Model\Repository\CommodityCodeProductRepository;

class Commodity
{
    /** @var Config */
    protected $config;

    /** @var CommodityCodeProductFactory */
    private $factory;

    /** @var ExceptionLogger */
    protected $logger;

    /** @var MetadataPool */
    private $metadataPool;

    /** @var CommodityCodeProductRepository */
    protected $repository;

    /** @var RequestInterface */
    private $request;

    /** @var ResourceConnection */
    private $resourceConnection;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        CommodityCodeProductRepository $repository,
        ExceptionLogger $logger,
        Config $config,
        CommodityCodeProductFactory $factory,
        RequestInterface $request
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
        $this->repository = $repository;
        $this->logger = $logger;
        $this->config = $config;
        $this->factory = $factory;
        $this->request = $request;
    }

    /**
     * Delete a Commodity Code given a Product ID
     *
     * @param int $productId
     * @return void
     */
    public function deleteByProductId($productId)
    {
        try {
            $this->repository->deleteByProductId($productId);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Retrieve the Commodity Code by Product ID
     *
     * @param int $productId
     * @return CommodityCodeProduct
     */
    public function getCodeModel($productId)
    {
        try {
            $commodityCode = $this->repository->getByProductId($productId);
        } catch (NoSuchEntityException $e) {
            $commodityCode = $this->factory->create();
            $commodityCode->setProductId($productId);
        }

        return $commodityCode;
    }

    /**
     * Retrieve the product row id by Product and version
     *
     * @param ProductInterface $product
     * @return string|null
     * @throws \Exception
     */
    public function getProductRowIdByVersionId(ProductInterface $product): ?string
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $updateId = $this->request->getParam('update_id');

        if (!$updateId) {
            return null;
        }

        $select = $this->resourceConnection->getConnection()
            ->select()
            ->from(['entity_table' => $metadata->getEntityTable()], [$this->getProductLinkField()])
            ->where('created_in <= ?', $updateId)
            ->where('updated_in > ?', $updateId)
            ->where('entity_id' . ' = ?', $product->getId())
            ->limit(1);
        return $this->resourceConnection->getConnection()->fetchOne($select) ?: null;
    }

    /**
     * Get the previous version id
     *
     * @param ProductInterface $product
     * @param int $version
     * @return string
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function getPreviousProductVersionId(ProductInterface $product, $version): ?string
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $select = $this->resourceConnection->getConnection()
            ->select()
            ->from(
                ['entity_table' => $metadata->getEntityTable()],
                [$this->getProductLinkField()]
            )
            ->where('created_in < ?', $version)
            ->where($metadata->getIdentifierField() . ' = ?', $product->getId())
            ->order('created_in DESC')
            ->setPart('disable_staging_preview', true)
            ->limit(1);

        return $this->resourceConnection->getConnection()->fetchOne($select) ?: null;
    }

    /**
     * Get the product entity link field
     *
     * @return string
     * @throws \Exception If entity type is not found.
     */
    public function getProductLinkField()
    {
        return $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
    }

    /**
     * Get the rollback version id
     *
     * @param ProductInterface $product
     * @param int $version
     * @return string
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function getRollbackProductVersionId(ProductInterface $product, $version): ?string
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        $select = $this->resourceConnection->getConnection()
            ->select()
            ->from(
                ['entity_table' => $metadata->getEntityTable()],
                [$this->getProductLinkField()]
            )
            ->where('created_in > ?', $version)
            ->where($metadata->getIdentifierField() . ' = ?', $product->getId())
            ->order('created_in ASC')
            ->setPart('disable_staging_preview', true)
            ->limit(1);

        return $this->resourceConnection->getConnection()->fetchOne($select) ?: null;
    }

    /**
     * Set rollback version data
     *
     * @param ProductInterface $product
     * @param int $version
     * @throws AlreadyExistsException
     * @throws \Zend_Db_Select_Exception
     */
    public function setRollBackData(ProductInterface $product, $version)
    {
        $previousVersionRowId = $this->getPreviousProductVersionId($product, $version);
        if (!$previousVersionRowId) {
            return;
        }

        $rollbackRowId = $this->getRollbackProductVersionId($product, $version);
        if (!$rollbackRowId) {
            return;
        }

        try {
            $commodityCodePrevious = $this->repository->getByProductId($previousVersionRowId);

            $commodityCodeRollback = $this->factory->create();
            $commodityCodeRollback->setProductId($rollbackRowId);
            $commodityCodeRollback->setType($commodityCodePrevious->getType());
            $commodityCodeRollback->setCode($commodityCodePrevious->getCode());

            $this->repository->save($commodityCodeRollback);
        } catch (NoSuchEntityException $exception) {
            $this->logger->critical($exception);
        } catch (CouldNotSaveException $exception) {
            $this->logger->critical($exception);
        }
    }
}
