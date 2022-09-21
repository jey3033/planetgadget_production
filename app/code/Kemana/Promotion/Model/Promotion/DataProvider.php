<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Model\Promotion;

use Kemana\Promotion\Model\ResourceModel\Promotion\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DataProvider
 *
 * @package Kemana\Promotion\Model\Promotion
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Kemana\BinFilter\Model\ResourceModel\Promotion\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $promotionCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $promotionCollectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $promotionCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Kemana\Promotion\Model\Promotion $promotion */
        foreach ($items as $promotion) {

            if ($promotion->getLandingImage()) {
                $landingImage = [];
                $landingImage[0] = [
                    'name' => str_replace('promotion/', '', $promotion->getLandingImage()),
                    'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $promotion->getLandingImage()
                ];

                $promotion->setLandingImage($landingImage);
            }

            if ($promotion->getLandingImageMobile()) {
                $landingImageMobile = [];
                $landingImageMobile[0] = [
                    'name' => str_replace('promotion/', '', $promotion->getLandingImageMobile()),
                    'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $promotion->getLandingImageMobile()
                ];

                $promotion->setLandingImageMobile($landingImageMobile);
            }

            $this->loadedData[$promotion->getId()] = $promotion->getData();
        }

        $data = $this->dataPersistor->get('kemana_promotion');

        if (!empty($data)) {

            $promotion = $this->collection->getNewEmptyItem();

            $promotion->setData($data);

            $this->loadedData[$promotion->getId()] = $promotion->getData();
            $this->dataPersistor->clear('promotion_id');
        }

        return $this->loadedData;
    }
}
