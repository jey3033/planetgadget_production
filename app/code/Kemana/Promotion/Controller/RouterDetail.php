<?php
/**
 * Copyright © 2021 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_Promotion
 * @license  Proprietary
 *
 * @author   Achintha Madushan <amadushan@kemana.com>
 */

namespace Kemana\Promotion\Controller; 

/**
 * Class RouterDetail
 *
 * @package Kemana\Promotion\Controller
 */
class RouterDetail implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $_actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Kemana\Promotion\Helper\Data
     */
    protected $promotionHelper;

    /**
     * RouterDetail constructor.
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Kemana\Promotion\Helper\Data $promotionHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Kemana\Promotion\Helper\Data $promotionHelper,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_scopeConfig = $scopeConfig;
        $this->_moduleManager = $moduleManager;
        $this->promotionHelper = $promotionHelper;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = current(explode("/", $identifier));

        $promotion = $this->promotionHelper->getPromotionIdForIdentifier($identifier, null, false);

        if (!empty($promotion)) {

            $postParams = [
                'promotion_id' => $promotion[array_key_first($promotion)]['promotion_id'],
                'title' => $promotion[array_key_first($promotion)]['title'],
                'meta_title' => $promotion[array_key_first($promotion)]['meta_title'],
                'meta_keywords' => $promotion[array_key_first($promotion)]['meta_keywords'],
                'meta_description' => $promotion[array_key_first($promotion)]['meta_description']
            ];

            $request->setModuleName('promotions')
                ->setControllerName('index')
                ->setActionName('detail')
                ->setPostValue($postParams);

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

            return $this->_actionFactory->create('Magento\Framework\App\Action\Forward');
        }
    }
}
