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
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

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
        ProductRepository $productRepository,
        Product $product,
        Configurable $configurable
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->sourceDataForSku = $sourceDataForSku;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->product = $product;
        $this->configurable = $configurable;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $sourceStockData = [];
        $response = $this->jsonFactory->create();
        $requestInfo = $this->request->getParams();

        $productIds = [];
        $productid = $requestInfo['product'];
        $addProduct = $this->product->load($productid);

        // simple product logic
        if($addProduct->getTypeId() == 'simple'){
            $productIds[] = $requestInfo['product'];
        }

        // configuration product logic
        if ($addProduct->getTypeId() == Configurable::TYPE_CODE) 
        {
            $attributes = $requestInfo['super_attribute'];
            $simple_product = $this->configurable->getProductByAttributes($attributes, $addProduct);
            if($simple_product){
                $productIds[] = $simple_product->getId();
            }
        }

        // bundle product logic
        if($addProduct->getTypeId() == 'bundle'){
            $buyRequest = new \Magento\Framework\DataObject($requestInfo);
            $cartCandidates = $addProduct->getTypeInstance()->prepareForCartAdvanced($buyRequest, $addProduct);
            foreach ($cartCandidates as $cartCandidate) {
                if ($cartCandidate->getTypeId() != 'bundle') {
                        $productIds[] = $cartCandidate->getId();
                }
            }
        }

        // gruop product logic
        if($addProduct->getTypeId() == 'grouped'){
            if(isset($requestInfo['super_group'])){
                foreach ($requestInfo['super_group'] as $product => $qty) {
                        $productIds[] = $product;       
                }
            }
        }        
        if($productIds){
            $result = [];
            foreach ($productIds as $key => $productid) {
                $product = $this->productRepository->getById($productid);
                $sku = $product->getData('sku');
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
                    $result[$sku] = $sourceStockData;
                }
            }
            return $response->setData(['locationData' => $result, 'response' => true]);
        }

        return $response->setData(['locationData' => [], 'response' => false]);
    }
}
