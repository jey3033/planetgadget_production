<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SourceDistanceShipping
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\SourceDistanceShipping\Model\Carrier;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Kemana\SourceDistanceShipping\Helper\Data;

/**
 * Custom shipping model
 */
class SourceDistanceBased extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'source_distance_based_shipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;

    protected $helper;

    protected $checkoutSession;

    protected $sourceLocation;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory,
        \Psr\Log\LoggerInterface                                    $logger,
        \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        Data                                                        $helper,
        CheckoutSession                                             $checkoutSession,
        \Kemana\SourceDistanceShipping\Model\Source\SourceLocation $sourceLocation,
        array                                                       $data = []
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->helper = $helper;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sourceLocation = $sourceLocation;
    }

    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /////////////////////////////

        //get the customer selected shipping address - destination in $request
        $customerShippingAddress = $this->getCustomerShippingAddress();

        //get all source locations


        // loop all source locations

        //check all items in cart and the quantities - if found insert into a array

        $itemsInCartWithQuantity = $this->getCartItemsWithQuantity();

        $sourceLocations = $this->sourceLocation->sourceLocationsToFullFillOrder($itemsInCartWithQuantity);




        //loop above array - get the address and get the distance between above customer address and this. if found locaiton under radiuse
        //of shipping method then show this shipping method
        foreach ($sourceLocations as $sourceData) {
            $d = $this->sourceLocation->getSourceLocationDetails($sourceData->getSourceCode());
            $gagg = 5;
        }


        /// /////////////////////////


        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = (float)$this->getConfigData('shipping_cost');

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerShippingAddress()
    {

        try {
            $customerShippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();

            return $this->helper->prepareAddressString($customerShippingAddress);

        } catch (\Exception $e) {
            $this->helper->log('Customer shipping address is not available ' . $e->getMessage());
        }

    }

    public function getCartItemsWithQuantity(): array
    {
        $itemData = [];

        $allItems = $this->checkoutSession->getQuote()->getAllItems();

        if (!empty($allItems)) {
            foreach ($allItems as $item) {
                $itemData[] = ['sku' => $item->getSku(), 'qty' => $item->getQty()];
            }
        }

        return $itemData;
    }
}
