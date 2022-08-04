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

namespace Kemana\StockAvailabilityPopup\Controller\Availability;

use Magento\Framework\Controller\Result\JsonFactory;
use Kemana\StockAvailabilityPopup\Model\Stock\SourceDataForSku;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Detail
 */
class Stock implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var SourceDataForSku
     */
    protected $sourceDataForSku;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param JsonFactory $jsonFactory
     * @param SourceDataForSku $sourceDataForSku
     * @param RequestInterface $request
     * @param ProductRepository $productRepository
     */
    public function __construct(
        JsonFactory      $jsonFactory,
        SourceDataForSku $sourceDataForSku,
        RequestInterface $request,
        ProductRepository $productRepository
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->sourceDataForSku = $sourceDataForSku;
        $this->request = $request;
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $sourceStockData = [];
        $response = $this->jsonFactory->create();

        $sku = $this->request->getParam('sku');

        $product = $this->productRepository->getById($this->request->getParam('id'));

        if (!$sku){
            $sku = $product->getData('sku');
        }

        $sourceData = $this->sourceDataForSku->getSourceItemBySku($sku);

        if (!empty($sourceData)) {

            foreach ($sourceData as $data) {
                $locationData = $this->sourceDataForSku->getSourceItemLocationData($data->getData('source_code'));

                if ($locationData && $locationData->getData('is_pickup_location_active')) {
                    $sourceStockData[] = [
                        'locationName' => $locationData->getData('name'),
                        'stockQty' => $data->getData('quantity'),
                        'street' => $locationData->getData('street'),
                        'region_id' => $locationData->getData('region_id'),
                        'region' => $locationData->getData('region'),
                        'city' => $locationData->getData('city'),
                        'postcode' => $locationData->getData('postcode'),
                        'city_id' => $locationData->getData('city_id'),
                        'district_id' => $locationData->getData('district_id'),
                        'district' => $locationData->getData('district'),
                        'phone' => $locationData->getData('phone')
                    ];
                }
            }

            return $response->setData(['locationData' => $sourceStockData, 'response' => true]);
        }

        return $response->setData(['locationData' => [], 'response' => false]);
    }
}
