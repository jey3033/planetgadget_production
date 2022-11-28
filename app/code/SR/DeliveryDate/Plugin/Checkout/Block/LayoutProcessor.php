<?php
namespace SR\DeliveryDate\Plugin\Checkout\Block;

use SR\DeliveryDate\Model\Config;

class LayoutProcessor
{
    /**
     * @var \SR\DeliveryDate\Model\Config
     */
    protected $config;

    /**
     * LayoutProcessor constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $options = [
            [   
                'value' => '',
                'label' => __('-- Select Pick Up Time --'),
            ]
        ];

        $min = $this->config->getHourMin();
        $max = $this->config->getHourMax();


        for ($i= $min; $i < $max ; $i++) { 

            $firsthour = $i;
            $nexthour = $firsthour + 1;

            $options[] = [
                'value' => $firsthour.':00 - '. $nexthour .':00',
                'label' => $firsthour.':00 - '. $nexthour .':00 ' . __('Available'),
            ];
        }

        $requiredDeliveryDate =  $this->config->getRequiredDeliveryDate()?: false;
        $jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']['store-selector']['children']['shippingAdditional'] = [
            'component' => 'uiComponent',
            'displayArea' => 'shippingAdditional',
            'children' => [
                'delivery_date' => [
                    'component' => 'SR_DeliveryDate/js/view/delivery-date-block',
                    'displayArea' => 'delivery-date-block',
                    'deps' => 'checkoutProvider',
                    'dataScopePrefix' => 'delivery_date',
                    'children' => [
                        'form-fields' => [
                            'component' => 'uiComponent',
                            'displayArea' => 'delivery-date-block',
                            'children' => [
                                'delivery_date' => [
                                    'component' => 'SR_DeliveryDate/js/view/delivery-date',
                                    'config' => [
                                        'customScope' => 'delivery_date',
                                        'template' => 'ui/form/field',
                                        'elementTmpl' => 'SR_DeliveryDate/fields/delivery-date',
                                        'options' => [],
                                        'id' => 'delivery_date',
                                        'data-bind' => ['datetimepicker' => true]
                                    ],
                                    'dataScope' => 'delivery_date.delivery_date',
                                    'label' => __('Pick Up Date'),
                                    'placeholder' => __('Day/Month/Year'),
                                    'provider' => 'checkoutProvider',
                                    'visible' => true,
                                    'validation' => [
                                        'required-entry' => $requiredDeliveryDate
                                    ],
                                    'sortOrder' => 10,
                                    'id' => 'delivery_date'
                                ],
                                'delivery_time' => [
                                    'component' => 'Magento_Ui/js/form/element/select',
                                    'config' => [
                                        'customScope' => 'delivery_date',
                                        'template' => 'ui/form/field',
                                        'elementTmpl' => 'ui/form/element/select',
                                        'options' => $options,
                                        'id' => 'delivery_time'
                                    ],
                                    'dataScope' => 'delivery_date.delivery_time',
                                    'label' => __('Pick Up Time'),
                                    'provider' => 'checkoutProvider',
                                    'visible' => true,
                                    'validation' => [],
                                    'sortOrder' => 20,
                                    'id' => 'delivery_time',
                                    'options' => $options,
                                ]
                            ],
                        ],
                    ]
                ]
            ]
        ];

        return $jsLayout;
    }
}