<?php

namespace Kemana\MsDynamics\Model;

class Customer
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder      $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder              $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder  $filterGroupBuilder
    )
    {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;

    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getNoySyncCustomersInPg($customerId = null)
    {
        if ($customerId && is_numeric($customerId)) {
            return $this->customerRepository->getById($customerId);
        }

        $filterErpCustomerNumber = $this->filterBuilder
            ->setField('ms_dynamic_customer_number')
            ->setConditionType('null')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filterErpCustomerNumber]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->customerRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSyncCustomersList($customerId = null)
    {
        if ($customerId) {
            return $this->customerRepository->getById($customerId);
        }

        $filterErpCustomerNumber = $this->filterBuilder
            ->setField('ms_dynamic_customer_number')
            ->setConditionType('notnull')
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filterErpCustomerNumber]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->customerRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param $email
     * @param $phoneNumber
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkExistEmailOrPhoneNumber($email, $phoneNumber)
    {

        $filterEmail = $this->filterBuilder->setField('email')
            ->setValue($email)
            ->setConditionType('eq')
            ->create();
        $filterPhoneNumber = $this->filterBuilder->setField('phonenumber')
            ->setValue($phoneNumber)
            ->setConditionType('eq')
            ->create();

        $filterOr = $this->filterGroupBuilder
            ->addFilter($filterEmail)
            ->addFilter($filterPhoneNumber)
            ->create();

        $this->searchCriteriaBuilder->setFilterGroups([$filterOr]);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        if (count($this->customerRepository->getList($searchCriteria)->getItems()) > 1) {
            return 'MULTIPLE';
        }

        if (count($this->customerRepository->getList($searchCriteria)->getItems())) {
            return true;
        }

        return false;
    }

}
