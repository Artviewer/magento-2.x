define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'RozetkaPay_RozetkaPay/js/action/set-payment-method-action'
        ],
    function (ko, $, Component, setPaymentMethodAction) {
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'RozetkaPay_RozetkaPay/payment/rozetkapay'
            },

            getDescription: function(){
                return window.checkoutConfig.rozetkapay.description;
            },

            afterPlaceOrder: function () {
                setPaymentMethodAction(this.messageContainer);
                return false;
            }
        });
    }
);
