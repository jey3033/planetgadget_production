<?php
/**
 * Kemana_Banner
 * @author Gayan Thrimanne<gthrimanne@kemana.com>
 * @see README.md
 *
 */

namespace Kemana\Banner\Cron;

use Kemana\Banner\Model\CleanCacheTags;

/**
 * Class SliderCacheClean
 * @package Kemana\Banner\Cron
 */
class SliderCacheClean
{
    /**
     * @var CleanCacheTags
     */
    protected $cleanCache;

    /**
     * SliderCacheClean constructor.
     * @param CleanCacheTags $cleanCache
     */
    public function __construct(
        CleanCacheTags $cleanCache
    ) {
        $this->cleanCache = $cleanCache;
    }

    /**
     * Function execute
     */
    public function execute()
    {
        $this->cleanCache->execute();
    }
}
