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

namespace Kemana\Jobs\Plugin\Jobs\Controller\Index;

/**
 * Class Filters
 */
class Filters
{
    
    /**
     * override existing template
     * @param \FME\Jobs\Controller\Index\Filters $subjects
     */
    public function afterExecute(\FME\Jobs\Controller\Index\Filters $subjects, $result)
    {
        $resultPage = $subjects->resultPageFactory->create();
        $resultJsonFactory = $subjects->resultJsonFactory->create();
        $filters = $subjects->getRequest()->getPostValue();
        $block = $resultPage->getLayout()
                ->createBlock('FME\Jobs\Block\Job')
                ->setTemplate('Kemana_Jobs::jobs/filters.phtml')                
                ->toHtml();        
        $resultJsonFactory->setData($block);
        return $resultJsonFactory;  
    }
}
