define(
    [
        'jquery',
        'mage/storage',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer'
        
    ],
    function ($, storage, urlBuilder, quote, customer) {
        'use strict';

        return function (deferred, ponumber) {
            var serviceUrl, payload;

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/ponumber/guest', {
                    cartId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    ponumber: ponumber
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/ponumber', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    ponumber: ponumber
                };
            }

            console.log(serviceUrl);
            
            return storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).done(
                function (data) {
                    if (data) {
                        deferred.resolve(data);
                    } else {
                        deferred.reject();
                    }
                }
            ).fail(
                function () {
                    deferred.reject();
                }
            );
        };
    }
);
