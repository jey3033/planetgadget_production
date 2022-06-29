<?php
/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_BlogArticle
 * @license  Proprietary
 *
 * @author   Kristian Claridad <kclaridad@kemana.com>
 */

namespace Kemana\BlogArticle\Helper;

use Magento\Framework\App\Helper\Context;
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
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @param Context $context
     * @param TimezoneInterface $s
     */
    public function __construct(
        Context               $context,
        TimezoneInterface     $timezoneInterface
    )
    {
        $this->_timezoneInterface = $timezoneInterface;

        parent::__construct($context);
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
