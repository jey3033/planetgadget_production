<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_Jobs
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Jobs\Plugin;

class ConfigUrlValidatePlugin
{
    public function aroundSave(
        \Magento\Config\Model\Config $configModel,
        \Closure $proceed
    ) {
        $path = 'jobs/job_seo_info/job_url_prefix';
        $groups = $configModel->getGroups();
        $url_prefix = $groups['job_seo_info']['fields']['job_url_prefix']['value'];   
        if($url_prefix){
            $filter_url_prefix = str_replace('/', '-', $url_prefix);
            $url = str_replace(' ', '-', $filter_url_prefix);
            $filter_url_prefix = preg_replace("![^a-z0-9]+!i", "-", $url);
            $configModel->setDataByPath($path, $filter_url_prefix);
        }
        return $proceed();
    }
}