var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'Kemana_ShippingInsurance/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Kemana_ShippingInsurance/js/view/shipping-mixin': true
            },
        }
    }
};