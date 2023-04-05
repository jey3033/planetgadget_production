<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Model;

use Kemana\Banner\Model\ResourceModel\SliderCron\CollectionFactory as SliderCronCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\PageCache\Model\Cache\Type;

/**
 * Class CleanCacheTags
 * @package Kemana\Banner\Model
 */
class CleanCacheTags
{
    /**
     * Const path enabled FPC
     */
    const XML_PATH_ENABLED_FPC = 'bannerslider/general/fullpage_cache';

    /**
     * COnst path cache tags
     */
    const XML_PATH_CACHE_TAGS = 'bannerslider/general/cache_tags';

    /**
     * @var Type
     */
    protected $cacheType;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SliderCronCollectionFactory
     */
    protected $sliderCronCollectionFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * CleanCacheTags constructor.
     * @param Type $cacheType
     * @param ScopeConfigInterface $scopeConfig
     * @param SliderCronCollectionFactory $sliderCronCollectionFactory
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Type $cacheType,
        ScopeConfigInterface $scopeConfig,
        SliderCronCollectionFactory $sliderCronCollectionFactory,
        TimezoneInterface $timezone
    ) {
        $this->cacheType = $cacheType;
        $this->scopeConfig = $scopeConfig;
        $this->sliderCronCollectionFactory = $sliderCronCollectionFactory;
        $this->timezone = $timezone;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $sliderCronCollection = $this->sliderCronCollectionFactory->create();
        $sliderCronCollection->addFieldToFilter(
            'scheduled_at',
            ['lteq' => $this->timezone->date()->format('Y-m-d H:i:s')]
        );

        if ($sliderCronCollection->getSize()) {
            $isFPC = $this->scopeConfig->getValue(self::XML_PATH_ENABLED_FPC);
            if ($isFPC) {
                $this->cacheType->clean();
            } else {
                $tagsValue = $this->scopeConfig->getValue(self::XML_PATH_CACHE_TAGS);
                $tags = explode(',', $tagsValue);
                $this->cacheType->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array_unique($tags));
            }
            $sliderCronCollection->walk('delete');
        }
    }
}
