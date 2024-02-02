define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'rozetkapay',
                component: 'RozetkaPay_RozetkaPay/js/view/payment/method-renderer/rozetkapay'
            }
        );
        return Component.extend({});
    }
);
