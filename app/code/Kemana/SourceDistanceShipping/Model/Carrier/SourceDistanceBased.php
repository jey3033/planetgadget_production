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

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

/**
 * Class SourceDistanceBased
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

    /**
     * @var \Kemana\SourceDistanceShipping\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Kemana\SourceDistanceShipping\Model\Source\SourceLocation
     */
    protected $sourceLocation;

    /**
     * @var \Kemana\SourceDistanceShipping\Model\Source\Distance
     */
    protected $distance;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Kemana\SourceDistanceShipping\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Kemana\SourceDistanceShipping\Model\Source\SourceLocation $sourceLocation
     * @param \Kemana\SourceDistanceShipping\Model\Source\Distance $distance
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory,
        \Psr\Log\LoggerInterface                                    $logger,
        \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Kemana\SourceDistanceShipping\Helper\Data                  $helper,
        \Magento\Checkout\Model\Session                            $checkoutSession,
        \Kemana\SourceDistanceShipping\Model\Source\SourceLocation $sourceLocation,
        \Kemana\SourceDistanceShipping\Model\Source\Distance $distance,
        array                                                       $data = []
    )
    {

        $this->helper = $helper;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->checkoutSession = $checkoutSession;
        $this->sourceLocation = $sourceLocation;
        $this->distance = $distance;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|\Magento\Shipping\Model\Rate\Result|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
        $sourceLocationsAddress = $this->sourceLocation->getSourceLocationsAddress($sourceLocations);

        foreach ($sourceLocationsAddress as $address) {
            $canShowMethod = $this->distance->checkDistanceFromShippingAddressToSourceLocation($customerShippingAddress, $address['address'], $this->getConfigData('distance'));

            if ($canShowMethod) {
                return $this->appendMethod();
                break;
            }
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function appendMethod() {

        $result = $this->rateResultFactory->create();
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title').' muntal');
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name').' olisan adral');

        $shippingCost = (float)$this->getConfigData('shipping_cost');

        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
    }

    /**
     * @return string|void|null
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

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCartItemsWithQuantity(): array
    {
        $itemData = [];

        $allItems = $this->checkoutSession->getQuote()->getItems();

        if (!empty($allItems)) {
            foreach ($allItems as $item) {
                $itemData[] = ['sku' => $item->getSku(), 'qty' => $item->getQty()];
            }
        }

        return $itemData;
    }
}
