<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Blog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   kemana team <jakartateam@kemana.com>
 */

namespace Kemana\Blog\Model\Import;

use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\UserFactory;
use Magento\Customer\Model\AccountManagementFactory;
use Kemana\Blog\Helper\Data as HelperData;
use Kemana\Blog\Helper\Image as HelperImage;
use Kemana\Blog\Model\CategoryFactory;
use Kemana\Blog\Model\CommentFactory;
use Kemana\Blog\Model\Config\Source\Import\Type;
use Kemana\Blog\Model\PostFactory;
use Kemana\Blog\Model\TagFactory;
use Kemana\Blog\Model\TopicFactory;

/**
 * Class Author
 * @package Kemana\Blog\Controller\Adminhtml
 */
abstract class AbstractImport extends AbstractModel
{
    /**
     * @var DateTime
     */
    public $date;

    /**
     * @var Type
     */
    public $importType;

    /**
     * @var HelperData
     */
    public $helperData;

    /**
     * @var PostFactory
     */
    protected $_postFactory;

    /**
     * @var TagFactory
     */
    protected $_tagFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var TopicFactory
     */
    protected $_topicFactory;

    /**
     * @var CommentFactory
     */
    protected $_commentFactory;

    /**
     * @var UserFactory
     */
    protected $_userFactory;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HelperImage
     */
    protected $_helperImage;

    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var Session
     */
    protected $_authSession;

    /**
     * Error Count Statistic
     * @var int
     */
    protected $_errorCount = 0;

    /**
     * Success Count Statistic
     * @var int
     */
    protected $_successCount = 0;

    /**
     * @var bool
     */
    protected $_hasData = false;

    /**
     * @var array
     */
    protected $_type;

    /**
     * @var AccountManagementFactory
     */
    protected $accountManagementFactory;

    /**
     * AbstractImport constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param PostFactory $postFactory
     * @param TagFactory $tagFactory
     * @param CategoryFactory $categoryFactory
     * @param TopicFactory $topicFactory
     * @param CommentFactory $commentFactory
     * @param UserFactory $userFactory
     * @param CustomerFactory $customerFactory
     * @param Session $authSession
     * @param ResourceConnection $resourceConnection
     * @param DateTime $date
     * @param Type $importType
     * @param HelperData $helperData
     * @param StoreManagerInterface $storeManager
     * @param HelperImage $helperImage
     * @param AccountManagementFactory $accountManagementFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostFactory $postFactory,
        TagFactory $tagFactory,
        CategoryFactory $categoryFactory,
        TopicFactory $topicFactory,
        CommentFactory $commentFactory,
        UserFactory $userFactory,
        CustomerFactory $customerFactory,
        Session $authSession,
        ResourceConnection $resourceConnection,
        DateTime $date,
        Type $importType,
        HelperData $helperData,
        StoreManagerInterface $storeManager,
        HelperImage $helperImage,
        AccountManagementFactory $accountManagementFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->date = $date;
        $this->importType = $importType;
        $this->_type = $this->_getImportType();
        $this->helperData = $helperData;
        $this->_postFactory = $postFactory;
        $this->_tagFactory = $tagFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_topicFactory = $topicFactory;
        $this->_commentFactory = $commentFactory;
        $this->_userFactory = $userFactory;
        $this->_customerFactory = $customerFactory;
        $this->_resourceConnection = $resourceConnection;
        $this->_authSession = $authSession;
        $this->_storeManager = $storeManager;
        $this->_helperImage = $helperImage;
        $this->accountManagementFactory = $accountManagementFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Import Post Function
     *
     * @param $data
     * @param $connection
     *
     * @return mixed
     */
    abstract protected function _importPosts($data, $connection);

    /**
     * Import Tag Function
     *
     * @param $data
     * @param $connection
     *
     * @return mixed
     */
    abstract protected function _importTags($data, $connection);

    /**
     * Import Category Function
     *
     * @param $data
     * @param $connection
     *
     * @return mixed
     */
    abstract protected function _importCategories($data, $connection);

    /**
     * Import Comment Function
     *
     * @param $data
     * @param $connection
     *
     * @return mixed
     */
    abstract protected function _importComments($data, $connection);

    /**
     * Import Author Function
     *
     * @param $data
     * @param $connection
     *
     * @return mixed
     */
    abstract protected function _importAuthors($data, $connection);

    /**
     * Get import statistics
     *
     * @param $type
     * @param $successCount
     * @param $errorCount
     * @param $hasData
     *
     * @return array
     */
    protected function _getStatistics($type, $successCount, $errorCount, $hasData)
    {
        $statistics = [
            'type' => $type,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'has_data' => $hasData
        ];

        return $statistics;
    }

    /**
     * Reset statistic record
     */
    protected function _resetRecords()
    {
        $this->_errorCount = 0;
        $this->_successCount = 0;
        $this->_hasData = false;
    }

    /**
     * Auto generate password
     *
     * @param int $length
     * @param bool $add_dashes
     * @param string $available_sets
     *
     * @return bool|string
     */
    protected function _generatePassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = [];
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }
        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }
        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }
        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);
        if (!$add_dashes) {
            return $password;
        }
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }

    /**
     * Get import types
     *
     * @return array
     */
    protected function _getImportType()
    {
        $types = [];
        foreach ($this->importType->toOptionArray() as $item) {
            $types[$item['value']] = $item['value'];
        }

        return $types;
    }
}
