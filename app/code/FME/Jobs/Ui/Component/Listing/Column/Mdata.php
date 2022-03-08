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
namespace FME\Jobs\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use FME\Jobs\Block\Adminhtml\Mdata\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class Mdata extends Column
{
    /** Url path */
    const JOBS_URL_PATH_EDIT = 'jobs/mdata/edit';
    const JOBS_URL_PATH_DELETE = 'jobs/mdata/delete';
    protected $actionUrlBuilder;
    protected $urlBuilder;
    private $editUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::JOBS_URL_PATH_EDIT
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['data_code'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['data_code' => $item['data_code']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::JOBS_URL_PATH_DELETE,
                            ['data_code' => $item['data_code']]
                        ),
                       'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete \"${ $.$data.data_name }\"'),
                            'message' => __('Are you sure you wan\'t to delete a \"${ $.$data.data_name }\" record?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
