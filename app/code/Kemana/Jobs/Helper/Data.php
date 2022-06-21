<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Jobs
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\Jobs\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;
    
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        Context               $context,
        FilterProvider        $filterProvider,
        StoreManagerInterface $storeManager,
        TimezoneInterface     $timezoneInterface
    )
    {
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_timezoneInterface = $timezoneInterface;

        parent::__construct($context);
    }

    /**
     * @param $content
     * @return mixed
     */
    public function formatJobContentToHtml($content)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($content);
    }

    /**
     * @param $dateTime
     * @param $formatString
     * @return string
     */
    public function getTimeAccordingToTimeZone($dateTime, $formatString)
    {
        $today = $this->_timezoneInterface->date()->format($formatString);    
        $dateTimeAsTimeZone = $this->_timezoneInterface
                                    ->date(new \DateTime($dateTime))
                                    ->format($formatString);
        return $dateTimeAsTimeZone;
    }
}
