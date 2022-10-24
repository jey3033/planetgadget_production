<?php
/**
 * Copyright © 2020 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_SocialLogin
 * @license  Proprietary
 *
 * @author   Aranga Wijesooriya <awijesooriya@kemana.com>
 */

namespace Kemana\Customer\Plugin\SocialLogin\SocialLogin\Model;

use Exception;
use Hybrid_Auth;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;

/**
 * Class Social
 *
 * @package Kemana\SocialLogin\Model
 */
class Social extends \Kemana\SocialLogin\Model\Social
{
    const STATUS_PROCESS = 'processing';

    const STATUS_LOGIN = 'logging';

    const STATUS_CONNECT = 'connected';

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @type \Kemana\SocialLogin\Helper\Social
     */
    protected $apiHelper;

    /**
     * @type
     */
    protected $apiName;

    /**
     * @var User
     */
    protected $_userModel;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var EmailNotificationInterface
     */
    protected $emailNotificationInterface;

    /**
     * Reward helper
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData;

    /**
     * Reward factory
     *
     * @var \Magento\Reward\Model\RewardFactory
     */
    protected $_rewardFactory;

    /**
     * Customer registry
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * Social constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CustomerFactory $customerFactory
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param \Kemana\SocialLogin\Helper\Social $apiHelper
     * @param User $userModel
     * @param DateTime $dateTime
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Random $mathRandom
     * @param AccountManagementInterface $accountManagement
     * @param EmailNotificationInterface $emailNotificationInterface
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Reward\Model\RewardFactory $rewardFactory,
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomerFactory $customerFactory,
        CustomerInterfaceFactory $customerDataFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        \Kemana\SocialLogin\Helper\Social $apiHelper,
        User $userModel,
        DateTime $dateTime,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Random $mathRandom,
        AccountManagementInterface $accountManagement,
        EmailNotificationInterface $emailNotificationInterface,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Reward\Model\RewardFactory $rewardFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry
    ) {
        $this->customerFactory            = $customerFactory;
        $this->customerRepository         = $customerRepository;
        $this->customerDataFactory        = $customerDataFactory;
        $this->storeManager               = $storeManager;
        $this->apiHelper                  = $apiHelper;
        $this->_userModel                 = $userModel;
        $this->_dateTime                  = $dateTime;
        $this->mathRandom                 = $mathRandom;
        $this->accountManagement          = $accountManagement;
        $this->emailNotificationInterface = $emailNotificationInterface;
        $this->_rewardData                = $rewardData;
        $this->_rewardFactory             = $rewardFactory;
        $this->customerRegistry           = $customerRegistry;
        parent::__construct(
            $context,
            $registry,
            $customerFactory,
            $customerDataFactory,
            $customerRepository,
            $storeManager,
            $apiHelper,
            $userModel,
            $dateTime,
            $resource,
            $resourceCollection,
            $data,
            $mathRandom,
            $accountManagement,
            $emailNotificationInterface
        );
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Kemana\SocialLogin\Model\ResourceModel\Social::class);
    }

    /**
     * @param $identify
     * @param $websiteId
     * @param $type
     * @return Customer
     */
    public function getCustomerBySocial($identify, $websiteId = null, $type)
    {
        $customer = $this->customerFactory->create();

        $socialCustomer = $this->getCollection()
            ->addFieldToFilter('social_id', $identify)
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('status', ['null' => 'true']);
        if ($websiteId) {
            $socialCustomer->addFieldToFilter('website_id', $websiteId);
        }
        $socialCustomer =  $socialCustomer->getFirstItem();

        if ($socialCustomer && $socialCustomer->getSocialCustomerId()) {
            $customer->load($socialCustomer->getCustomerId());
        }

        return $customer;
    }

    /**
     * @param $email
     * @param null $websiteId
     *
     * @return Customer
     * @throws LocalizedException
     */
    public function getCustomerByEmail($email, $websiteId = null)
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId ?: $this->storeManager->getWebsite()->getId());
        $customer->loadByEmail($email);

        return $customer;
    }
    /**
     * @param $data
     * @param $store
     *
     * @return mixed
     * @throws Exception
     */
    public function createCustomerSocial($data, $store)
    {

        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->customerDataFactory->create();
        $phonenumber = $this->convertPhonenumber($data['phonenumber']);
        $customer->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setStoreId($store->getId())
            ->setWebsiteId($store->getWebsiteId())
            ->setCreatedIn($store->getName())
            ->setCustomAttribute('phonenumber', $phonenumber)
            ->setDob($data['dob']);

        $websiteId = $store->getWebsiteId();

        try {
            if ($data['password'] !== null) {
                $customer = $this->customerRepository->save($customer, $data['password']);
                $this->getEmailNotification()->newAccount(
                    $customer,
                    $this->emailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED
                );
                $this->setRewardsAfterSocialLoginCreateAccount($customer);
            } else {
                // If customer exists existing hash will be used by Repository
                $customer = $this->customerRepository->save($customer);

                $newPasswordToken  = $this->mathRandom->getUniqueHash();
                $this->accountManagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
                $this->setRewardsAfterSocialLoginCreateAccount($customer);
            }

            if ($this->apiHelper->canSendPassword($store)) {
                $this->getEmailNotification()->newAccount(
                    $customer,
                    $this->emailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD
                );
            }

            $this->setAuthorCustomer($data['identifier'],$websiteId, $customer->getId(), $data['type']);
        } catch (AlreadyExistsException $e) {
            throw new InputMismatchException(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (Exception $e) {
            if ($customer->getId()) {
                $this->_registry->register('isSecureArea', true, true);
                $this->customerRepository->deleteById($customer->getId());
            }
            throw $e;
        }


        /**
         * @var Customer $customer
         */
        $customer = $this->customerFactory->create()->load($customer->getId());

        return $customer;
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        return $this->emailNotificationInterface;
    }

    /**
     * @param $identifier
     * @param $websiteId
     * @param $customerId
     * @param $type
     * @return $this
     * @throws Exception
     */
    public function setAuthorCustomer($identifier, $websiteId = null,  $customerId, $type)
    {
        $this->setData(
            [
                'social_id'              => $identifier,
                'customer_id'            => $customerId,
                'type'                   => $type,
                'is_send_password_email' => $this->apiHelper->canSendPassword(),
                'social_created_at'      => $this->_dateTime->date(),
                'website_id'             => $websiteId
            ]
        )
            ->setId(null)->save();

        return $this;
    }

    /**
     * @param $apiName
     * @param null $area
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getUserProfile($apiName, $area = null)
    {
        $config = [
            'base_url'   => $this->apiHelper->getBaseAuthUrl($area),
            'providers'  => [
                $apiName => $this->getProviderData($apiName)
            ],
            'debug_mode' => false,
            'debug_file' => BP . '/var/log/social.log'
        ];

        $auth = new Hybrid_Auth($config);

        try {
            $adapter     = $auth->authenticate($apiName);
            $userProfile = $adapter->getUserProfile();
        } catch (Exception $e) {
            $auth->logoutAllProviders();
            $auth        = new Hybrid_Auth($config);
            $adapter     = $auth->authenticate($apiName);
            $userProfile = $adapter->getUserProfile();
        }

        return $userProfile;
    }

    /**
     * @param $apiName
     *
     * @return array
     */
    public function getProviderData($apiName)
    {
        $data = [
            'enabled' => $this->apiHelper->isEnabled(),
            'keys'    => [
                'id'     => $this->apiHelper->getAppId(),
                'key'    => $this->apiHelper->getAppId(),
                'secret' => $this->apiHelper->getAppSecret()
            ]
        ];

        return array_merge($data, $this->apiHelper->getSocialConfig($apiName));
    }

    /**
     * @param $identify
     * @param $type
     *
     * @return User
     */
    public function getUserBySocial($identify, $type)
    {
        $user = $this->_userModel;

        $socialCustomer = $this->getCollection()
            ->addFieldToFilter('social_id', $identify)
            ->addFieldToFilter('type', $type)->addFieldToFilter('user_id', ['notnull' => true])
            ->getFirstItem();

        if ($socialCustomer && $socialCustomer->getId()) {
            $user->load($socialCustomer->getUserId());
        }

        return $user;
    }

    /**
     * @param $type
     * @param $identifier
     *
     * @return DataObject
     */
    public function getUser($type, $identifier)
    {
        return $this->getCollection()
            ->addFieldToSelect('user_id')
            ->addFieldToSelect('social_customer_id')
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('social_id', base64_decode($identifier))
            ->addFieldToFilter('status', self::STATUS_LOGIN)
            ->getFirstItem();
    }

    /**
     * @param $socialCustomerId
     * @param $identifier
     *
     * @return $this
     * @throws Exception
     */
    public function updateAuthCustomer($socialCustomerId, $identifier)
    {
        $social = $this->load($socialCustomerId);
        $social->addData(
            [
                'social_id' => $identifier,
                'status'    => self::STATUS_CONNECT
            ]
        );
        $social->save();

        return $this;
    }

    /**
     * @param $socialCustomerId
     * @param $status
     *
     * @return $this
     * @throws Exception
     */
    public function updateStatus($socialCustomerId, $status)
    {
        $social = $this->load($socialCustomerId);
        $social->addData(['status' => $status])->save();

        return $this;
    }

    /**
     * * convert phonenumber with first digit 0 to 62
     * @param $phonenumber
     * @return string
     */
    public function convertPhonenumber($phonenumber)
    {

        $newphonenumber = '-';
        if ($phonenumber) {
            $newphonenumber = $phonenumber;
            $pnFirstDigit = substr($phonenumber, 0, 1); // get first digit in phonenumber

            if($pnFirstDigit == 0){
                $ptn = "/^0/";  // Regex first digit if 0
                $rpltxt = "62";  // Replacement string
                $newphonenumber =  preg_replace($ptn, $rpltxt, $phonenumber); // replace first digit if 0
            }
        }
        return $newphonenumber;
    }

    /**
     * set register reward points after customer account create
     * @param $customer
     * @return null
     */
    public function setRewardsAfterSocialLoginCreateAccount($customer)
    {
        $subscribeByDefault = $this->_rewardData->getNotificationConfig(
            'subscribe_by_default',
            $this->storeManager->getStore()->getWebsiteId()
        );

        try {
            $customerModel = $this->customerRegistry
                ->retrieveByEmail($customer->getEmail());
            $customerModel->setRewardUpdateNotification($subscribeByDefault);
            $customerModel->setRewardWarningNotification($subscribeByDefault);
            $customerModel->getResource()
                ->saveAttribute($customerModel, 'reward_update_notification');
            $customerModel->getResource()
                ->saveAttribute($customerModel, 'reward_warning_notification');

            $this->_rewardFactory->create()->setCustomer(
                $customer
            )->setActionEntity(
                $customer
            )->setStore(
                $this->storeManager->getStore()->getId()
            )->setAction(
                \Magento\Reward\Model\Reward::REWARD_ACTION_REGISTER
            )->updateRewardPoints();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
