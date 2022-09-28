/**
 * @category Kemana
 * @package  Kemana_Snap
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  Rizal Fauzie Ridwan <rfridwan@kemana.com>, Cipto Raharjo <craharjo@kemana.com>
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list',
    'uiLayout',
    'uiRegistry',
    'underscore'
], function (
    Component,
    rendererList,
    layout,
    registry,
    _
) {
    'use strict';

    var snap = window.checkoutConfig.payment.snap;

    if (!snap.channel && snap.active) {
        rendererList.push({
            type: 'snap',
            component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method'
        });
    } else
    if (!_.isEmpty(snap.channel)) {
        _.each(snap.channel, function(data, method) {
            rendererList.push({
                type: method,
                component: data.component
            });
        });
    }

    var customGroupName = 'customGroup';
    layout([{
        name: customGroupName,
        component: 'Kemana_Snap/js/model/payment/method-group-custom',
    }]);
    registry.get(customGroupName, function (customGroup) {
        rendererList.push(
            {
                type: 'indodanapayment',
                component: 'Indodana_PayLater/js/view/payment/method-renderer/indodanapayment',
                group: customGroup
            },
             {
                type: 'checkmo',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/checkmo-method',
                group: customGroup
            },
            {
                type: 'banktransfer',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/banktransfer-method',
                group: customGroup
            },
            {
                type: 'cashondelivery',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/cashondelivery-method',
                group: customGroup
            },
            {
                type: 'purchaseorder',
                component: 'Magento_OfflinePayments/js/view/payment/method-renderer/purchaseorder-method',
                group: customGroup
            },
            {
                type: 'kredivopayment',
                component: 'Kredivo_Payment/js/view/payment/method-renderer/kredivopayment-method',
                group: customGroup
            },
            {
                type: 'snap_gopay',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_telkomsel_cash',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_briepay',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_mandiriclickpay',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_cimbclicks',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_bcaklikpay',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_klikbca',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_danamon_online',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_allbank_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_indomaret',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_permata_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_mandiri_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_bni_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_bri_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_maybank_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_ocbcnisp_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_hsbc_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_citibank_cc',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method-installment',
                group: customGroup
            },
            {
                type: 'snap_bcava',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_mandiribill',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_permatava',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_bniva',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_briva',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_otherva',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_alfamart',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            },
            {
                type: 'snap_akulaku',
                component: 'Kemana_Snap/js/view/payment/method-renderer/snap-method',
                group: customGroup
            }
        );
    });
        

    return Component.extend({});
});
