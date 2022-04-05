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
namespace FME\Jobs\Ui\Component\Listing\Column\Job;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Department extends Column
{
	protected $mFactory;

    public function __construct(
    	\FME\Jobs\Model\Job $mFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
    	$this->mFactory = $mFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
    	$mCollection = $this->mFactory->getDepartments();
    	foreach($mCollection as $miniCollec)
    	{	
            if (isset($dataSource['data']['items'])) {
                foreach ($dataSource['data']['items'] as &$items) {
                    if ($items['jobs_select_departments'] == $miniCollec['data_code']) {
                        $items['jobs_select_departments'] = $miniCollec['data_name'];
                    } 
                }//end foreach inner
            }//end foreach outer    
        }
        return $dataSource;
    }
}