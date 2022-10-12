<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Kemana\Insurance\Preference\Model\Cart;

use Magento\Quote\Api;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Api\CouponManagementInterface;
use Magento\Quote\Api\Data\TotalsInterface as QuoteTotalsInterface;
use Magento\Quote\Model\Cart\TotalsConverter; 
use Magento\Framework\App\Config\ScopeConfigInterface;
/**
 * Cart totals data object.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartTotalRepository implements CartTotalRepositoryInterface
{
    /**
     * Cart totals factory.
     *
     * @var Api\Data\TotalsInterfaceFactory
     */
    private $totalsFactory;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ItemConverter
     */
    private $itemConverter;

    /**
     * @var CouponManagementInterface
     */
    protected $couponService;

    /**
     * @var TotalsConverter
     */
    protected $totalsConverter;

    /**
     * insurance fee is enabled
     */
    protected $insurancepath = 'insurance_fee/general/';

    /**
     * @param Api\Data\TotalsInterfaceFactory $totalsFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CouponManagementInterface $couponService
     * @param TotalsConverter $totalsConverter
     * @param ItemConverter $converter
     */
    public function __construct(
        Api\Data\TotalsInterfaceFactory $totalsFactory,
        CartRepositoryInterface $quoteRepository,
        DataObjectHelper $dataObjectHelper,
        CouponManagementInterface $couponService,
        TotalsConverter $totalsConverter,
        ItemConverter $converter,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->totalsFactory = $totalsFactory;
        $this->quoteRepository = $quoteRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->couponService = $couponService;
        $this->totalsConverter = $totalsConverter;
        $this->itemConverter = $converter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function get($cartId): QuoteTotalsInterface
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual()) {
            $addressTotalsData = $quote->getBillingAddress()->getData();
            $addressTotals = $quote->getBillingAddress()->getTotals();
        } else {
            $addressTotalsData = $quote->getShippingAddress()->getData();
            $addressTotals = $quote->getShippingAddress()->getTotals();
        }
        unset($addressTotalsData[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
        /** @var QuoteTotalsInterface $quoteTotals */
        $quoteTotals = $this->totalsFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteTotals,
            $addressTotalsData,
            QuoteTotalsInterface::class
        );
        $items = array_map([$this->itemConverter, 'modelToDataObject'], $quote->getAllVisibleItems());
        $calculatedTotals = $this->totalsConverter->process($addressTotals);
        $quoteTotals->setTotalSegments($calculatedTotals);

        $amount = $quoteTotals->getGrandTotal() - $quoteTotals->getTaxAmount();
        $amount = $amount > 0 ? $amount : 0;
        $quoteTotals->setCouponCode($this->couponService->get($cartId));
        $quoteTotals->setGrandTotal($amount);
        $quoteTotals->setItems($items);
        $quoteTotals->setItemsQty($quote->getItemsQty());
        $quoteTotals->setBaseCurrencyCode($quote->getBaseCurrencyCode());
        $quoteTotals->setQuoteCurrencyCode($quote->getQuoteCurrencyCode());

        // insurance calculation 
        if($this->getConfigValue("active")){
            $method = $quote->getShippingAddress()->getShippingMethod();
            if(isset($method) && (strpos($method, "jnt_") === 0 || strpos($method, "jne_") === 0)){
                $fee = $quote->getSubtotal() * 0.002;
                if(strpos($method, "jne_") === 0){
                    $extra_fee = $this->getConfigValue("extra_fee") ? $this->getConfigValue("extra_fee") : 0;
                    $fee = $fee + $extra_fee;
                }
                $quoteTotals->setShippingAmount($quoteTotals->getShippingAmount() + $fee);
                $quoteTotals->setGrandTotal($amount + $fee);
            }
        }

        return $quoteTotals;
    }

    /**
     * @return mixed
     */
    public function getConfigValue($subpath)
    {
       return $this->scopeConfig->getValue($this->insurancepath.$subpath);
    }
}
