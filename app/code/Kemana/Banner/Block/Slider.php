<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Kemana\Banner\Helper\Data as bannerHelper;

/**
 * Class Slider
 * @package Kemana\Banner\Block
 */
class Slider extends Template
{
    /**
     * @type bannerHelper
     */
    public $helperData;

    /**
     * @type StoreManagerInterface
     */
    protected $store;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * Slider constructor.
     *
     * @param Template\Context $context
     * @param bannerHelper $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $dateTime
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        bannerHelper $helperData,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
        $this->store = $context->getStoreManager();
        $this->_date = $dateTime;
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Kemana_Banner::bannerslider.phtml');
    }

    /**
     * Get Slider Id
     * @return string
     */
    public function getSliderId()
    {
        if ($this->getSlider()) {
            return $this->getSlider()->getSliderId();
        }

        return time();
    }

    /**
     * @param $content
     *
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter($content);
    }

    /**
     * @return array|AbstractCollection
     */
    public function getBannerCollection()
    {
        $collection = [];
        if ($this->getSliderId()) {
            $collection = $this->helperData->getBannerCollection($this->getSliderId())->addFieldToFilter('status', 1);
        }

        return $collection;
    }

    /**
     * @return false|string
     */
    public function getBannerOptions()
    {
        return $this->helperData->getBannerOptions($this->getSlider());
    }
}
