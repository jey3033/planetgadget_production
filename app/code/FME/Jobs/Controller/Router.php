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
namespace FME\Jobs\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    protected $actionFactory;
    protected $_response;
    protected $_request;
    protected $pageRepository;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \FME\Jobs\Helper\Job $helper,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_request = $request;
        $this->pageRepository = $pageRepository;
        $this->_response = $response;
        $this->articlesHelper = $helper;
    }
    
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $route = $this->articlesHelper->getJobSeoPrefix();
        $suffix = $this->articlesHelper->getjobseoSuffix();
        $identifier = trim($request->getPathInfo(), '/');
        $parts = explode('/', $identifier);
        $identifie = $route.$suffix;
        $identifieDetail = 'detail';   
        if (strcmp($identifier, $identifie) == 0) {
            $request->setModuleName('job')->setControllerName('Index')->setActionName('Index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        } elseif (isset($parts[0]) && ($parts[0] == $route) && ($parts[1] !== 'application') && isset($parts[1]) && !isset($parts[2])) {
              $detailIdentifier =  $parts[1];
            if (strpos($detailIdentifier, '.') !== false) {
                $detailIdentifier = explode('.', $detailIdentifier);
                $detailIdentifier = $detailIdentifier[0];
            }
            $request->setModuleName('job')->setControllerName('Index')->setActionName('Detail')->setParam('id', $detailIdentifier);
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        }
        elseif (isset($parts[0]) && ($parts[0] == $route) && isset($parts[1]) && $parts[1] == 'application' ) {  
               
            $request->setModuleName('job')->setControllerName('Index')->setActionName('Application');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        }
        else {
            return null;
        }                
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}
