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
namespace FME\Jobs\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\ObjectManagerInterface;

class Job extends AbstractHelper
{
    protected $_timezoneInterface;
    const JOB_MODULE_ENABLED               =   'jobs/job_extension/jobs_mod_enable';    
    const JOB_HEADER_LINK_TITLE            =   'jobs/basic_configs/job_header_link';
    const JOB_HEADER_LINK_ENABLE           =   'jobs/basic_configs/job_header_link_enable';
    const JOB_BOTTOM_LINK_ENABLE           =   'jobs/basic_configs/job_bottom_link_enable';
    const JOB_BOTTOM_LINK_TITLE            =   'jobs/basic_configs/job_bottom_link';
    const JOB_MAIN_TITLE                 = 'jobs/basic_configs/jobs_module_label';
    const JOB_MAIN_DESCRIPTION                    = 'jobs/basic_configs/jobs_module_description';
    const JOB_SIDE_BAR                    = 'jobs/basic_configs/job_side_bar_enable';
    const JOB_TOP_BAR                    = 'jobs/basic_configs/job_top_bar_enable';
    const JOB_EXPIRED_JOBS              = 'jobs/basic_configs/job_expired_jobs_show';    
    const JOB_FILTERS_SHOWMORE              = 'jobs/basic_configs/jobs_sidebar_count_showmore';
    const JOB_EMAIL_SENDER                  = 'jobs/email/sender_email_identity';
    const JOB_EMAIL_Receiver                  = 'jobs/email/receiver_email_identity';
    const JOB_SENDER_EMAIL_TEMPLATE                   = 'jobs/email/email_template';
    const JOB_FORM_POPUP                = 'jobs/job_detail/job_form_popup';
    const JOB_DETAIL_SHARING_OPT                = 'jobs/job_detail/job_sharing_options';
    const JOB_GOOGLE_CAPTCHA              = 'jobs/job_detail/job_google_captcha';    
    const JOB_GOOGLE_CAPTCHA_ENABLE              = 'jobs/job_detail/job_enable_capthca';    
    const JOB_PAGE_TITLE_SEO               =   'jobs/job_seo_info/job_page_title';
    const JOB_PAGE_METAKEYWORD_SEO         =   'jobs/job_seo_info/job_meta_keywords';
    const JOB_PAGE_METADESCRIPTION_SEO     =   'jobs/job_seo_info/job_meta_description';
    const JOB_URL_PREFIX_SEO               =   'jobs/job_seo_info/job_url_prefix';
    const JOB_URL_SUFFIX_SEO               =   'jobs/job_seo_info/job_url_suffix';    
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \FME\Jobs\Model\JobFactory $jobFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
    ) 
    {
        $this->_timezoneInterface = $timezoneInterface;
        $this->_jobFactory = $jobFactory;
        parent::__construct($context);
    }

    public function getTimeAccordingToTimeZone($dateTime)
    {
        $today = $this->_timezoneInterface->date()->format('m/d/y H:i:s');    
        $dateTimeAsTimeZone = $this->_timezoneInterface
                                        ->date(new \DateTime($dateTime))
                                        ->format('m/d/y H:i:s');
        return $dateTimeAsTimeZone;
    }

    public function isJobModuleEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::JOB_MODULE_ENABLED, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == 0) {
            $isEnabled = false;
        }
        return $isEnabled;
    }    
    
    public function isJobHeaderLinkEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_HEADER_LINK_ENABLE, ScopeInterface::SCOPE_STORE);
    }
    public function isJobBottomLinkEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_BOTTOM_LINK_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    public function getJobModHeading()
    {
        return $this->scopeConfig->getValue(self::JOB_MAIN_TITLE, ScopeInterface::SCOPE_STORE);
    }

    public function getJobMainDescription()
    {
        return $this->scopeConfig->getValue(self::JOB_MAIN_DESCRIPTION, ScopeInterface::SCOPE_STORE);
    }
    
    
    public function getJobSideBarEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_SIDE_BAR, ScopeInterface::SCOPE_STORE);
    }

    public function getJobTopBarEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_TOP_BAR, ScopeInterface::SCOPE_STORE);
    }
    
    public function getJobExpiredStatus()
    {
        return $this->scopeConfig->getValue(self::JOB_EXPIRED_JOBS, ScopeInterface::SCOPE_STORE);
    }
    public function getSenderEmail()
    {
        return $this->scopeConfig->getValue(self::JOB_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
    }
    public function getReceiverEmail()
    {
        return $this->scopeConfig->getValue(self::JOB_EMAIL_Receiver, ScopeInterface::SCOPE_STORE);
    }
    public function jobHeaderLinkTitle()
    { 
        if (self::isJobHeaderLinkEnable()) {
            return $this->scopeConfig->getValue(self::JOB_HEADER_LINK_TITLE, ScopeInterface::SCOPE_STORE);
        }
    }

    public function jobBottomLinkTitle()
    {
        if (self::isJobBottomLinkEnable()) {
            return $this->scopeConfig->getValue(self::JOB_BOTTOM_LINK_TITLE, ScopeInterface::SCOPE_STORE);
        }
    }
    
    public function getSenderEmailTemplate()
    {
        return $this->scopeConfig->getValue(self::JOB_SENDER_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
    }
    
    public function getPopupEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_FORM_POPUP, ScopeInterface::SCOPE_STORE);
    }
    
    public function getJobSharingOptions()
    {
        return $this->scopeConfig->getValue(self::JOB_DETAIL_SHARING_OPT, ScopeInterface::SCOPE_STORE);
    }
    
    public function getNumForShowMore()
    {
        return $this->scopeConfig->getValue(self::JOB_FILTERS_SHOWMORE, ScopeInterface::SCOPE_STORE);
    }   
    
    public function getJobPageTitleSeo()
    {
        return $this->scopeConfig->getValue(self::JOB_PAGE_TITLE_SEO, ScopeInterface::SCOPE_STORE);
    }
    public function getJobPageMetakeywordSeo()
    {
        return $this->scopeConfig->getValue(self::JOB_PAGE_METAKEYWORD_SEO, ScopeInterface::SCOPE_STORE);
    }
    
    public function getJobPageMetadescriptionSeo()
    {
        return $this->scopeConfig->getValue(self::JOB_PAGE_METADESCRIPTION_SEO, ScopeInterface::SCOPE_STORE);
    }
    
    public function getJobSeoPrefix()
    {
        return $this->scopeConfig->getValue(self::JOB_URL_PREFIX_SEO, ScopeInterface::SCOPE_STORE);
    }

    public function getjobseoSuffix()
    {
        return $this->scopeConfig->getValue(self::JOB_URL_SUFFIX_SEO, ScopeInterface::SCOPE_STORE);
    }
    
    public function getjobCategorySeo()
    {
        return $this->scopeConfig->getValue(self::job_CATEGORY_URL_SEO, ScopeInterface::SCOPE_STORE);
    }

    public function getjobFinalIdentifier()
    {
        if ($this->getJobSeoPrefix()) {
            return $this->getjobseoPrefix().$this->getjobseoSuffix();
        } else {
            return 'job';
        }
    }
    
    public function getJobFinalDetailIdentifier($detailId)
    {
        if ($this->getJobSeoPrefix()) {
            return $this->getJobSeoPrefix().'/'.$detailId.$this->getjobseoSuffix();
        } else {
            return 'job/'.$detailId.$this->getjobseoSuffix();
        }
    }

    public function getjobFinalCategoryIdentifier($detailId)
    {
        if ($this->getjobseoPrefix()) {
            return $this->getjobseoPrefix().'/cat/'.$detailId.$this->getjobseoSuffix();
        } else {
            return 'job/'.'cat/'.$detailId.$this->getjobseoSuffix();
        }
    }
    
    
    public function getJobLink()
    {
        $identifier = $this->getjobseoPrefix();
        $seo_suffix = $this->getjobseoSuffix();
        if (isset($identifier) && isset($seo_suffix)) {
            return $identifier.$seo_suffix;
        } else {
            return 'job';
        }
    }
    
    public function isJobsCaptchaEnable()
    {
        return $this->scopeConfig->getValue(self::JOB_GOOGLE_CAPTCHA_ENABLE, ScopeInterface::SCOPE_STORE);
    }
    
    public function getJobsCaptchaKey()
    {
        return $this->scopeConfig->getValue(self::JOB_GOOGLE_CAPTCHA, ScopeInterface::SCOPE_STORE);
    }

    public function getjobsharingOption()
    {
        return $this->scopeConfig->getvalue(self::job_SHARING_OPTIONS, ScopeInterface::SCOPE_STORE);
    }
    
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );
    }
    public function getSingleJobById($jobsid){
        $active=1;
        return $this->_jobFactory->create()->getCollection()->addFieldToFilter('jobs_id',array('eq' => $jobsid))->addFieldToFilter('is_active', array('eq'=> $active));
    }
}
