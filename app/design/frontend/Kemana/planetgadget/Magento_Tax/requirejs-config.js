/**
 * Copyright © 2022 PT Kemana Teknologi Solusi. All rights reserved.
 * http://www.kemana.com
 */

/**
 * @category Kemana
 * @package  Kemana_MsDynamics
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Parth Godhani <pgodhani@kemana.com>
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/shipping': {
                'Magento_Tax/js/shipping': true
            }
        }
    }
};
