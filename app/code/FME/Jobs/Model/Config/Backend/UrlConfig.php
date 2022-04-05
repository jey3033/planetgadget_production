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
namespace FME\Jobs\Model\Config\Backend;

class UrlConfig extends \Magento\Framework\App\Config\Value{
    public function __construct(
    	\Magento\Framework\Model\Context $context,
	    \Magento\Framework\Registry $registry,
	    \Magento\Framework\App\Config\ScopeConfigInterface $config,
	    \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
	    \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
	    \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
	    array $data = []
    ){
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

	public function beforeSave()
    {
        $value = $this->getValue();
        if (trim($value) == '') {
            throw new \Magento\Framework\Exception\ValidatorException(__('Slug is required.'));
        } else {
            $value = str_replace('/', '-', $value);
            $value = str_replace(' ', '-', $value);
        }
        $this->setValue($value);
        parent::beforeSave();
    }
}

