<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Customer
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Customer\Block\Address;

use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Customer address grid
 */
class Grid extends \Magento\Customer\Block\Address\Grid
{

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    private $addressCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\Collection
     */
    private $addressCollection;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param CountryFactory $countryFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        AddressCollectionFactory $addressCollectionFactory,
        CountryFactory $countryFactory,
        AddressRepositoryInterface $addressRepository,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->countryFactory = $countryFactory;
        $this->addressRepository = $addressRepository;

        parent::__construct($context, $currentCustomer, $addressCollectionFactory, $countryFactory, $data);
    }

    /**
     * Render an address as HTML and return the result
     *
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressMapPoint(\Magento\Customer\Api\Data\AddressInterface $address = null)
    {
        try {
            if ($address) {
                if ($map_point = $address->getCustomAttribute('map_point')) {
                    if (!empty($map_point->getValue())) {
                        return "Already Pinpoint";
                    }
                } else {
                    return "Not Pinpoint yet";
                }
            }
        } catch (NoSuchEntityException $e) {
            $address = '';
        }
        return '';
    }

    /**
     * Get customer address by ID
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressById($addressId)
    {
        try {
            return $this->addressRepository->getById($addressId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get customer addresses collection.
     *
     * Filters collection by customer id
     *
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     * @throws NoSuchEntityException
     */

    public function getAdditionalAddress(): \Magento\Customer\Model\ResourceModel\Address\Collection
    {
        if (null === $this->addressCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }
            /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $collection */
            $collection = $this->addressCollectionFactory->create();
            $collection->setOrder('entity_id', 'desc');
            $collection->addFieldToFilter(
                'entity_id',
                ['nin' => [$this->getDefaultBilling(), $this->getDefaultShipping()]]
            );
            $collection->setCustomerFilter([$this->getCustomer()->getId()]);
            return $collection;
        }
        return $this->addressCollection;
    }

    /**
     * Get current additional customer addresses
     *
     * Return array of address interfaces if customer has additional addresses and false in other cases
     *
     * @return \Magento\Customer\Api\Data\AddressInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NoSuchEntityException
     * @since 102.0.1
     */
    public function getAdditionalAddresses(): array
    {
        $additional = [];
        $addresses = $this->getAdditionalAddress();
        $primaryAddressIds = [$this->getDefaultBilling(), $this->getDefaultShipping()];
        foreach ($addresses as $address) {
            if (!in_array((int)$address->getId(), $primaryAddressIds, true)) {
                $additional[] = $address->getDataModel();
            }
        }
        return $additional;
    }
}
