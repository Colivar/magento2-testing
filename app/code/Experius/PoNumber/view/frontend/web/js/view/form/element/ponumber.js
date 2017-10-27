define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Experius_Ponumber/js/action/ponumber',
    'uiRegistry'
], function ($,Abstract,quote, checkoutData,savePonumber,registry) {
    return Abstract.extend({
        defaults: {
          listens: {
                value: 'ponumberHasChanged',
                //focused: 'ponumberHasChanged',
          },
          checkDelay: 2000,
          emailCheckTimeout: 0,
          isLoading: false,
          checkRequest: null,
          isPonumberCheckComplete: null,
          value: window.checkoutConfig.quoteData.experius_po_number
        },
        ponumberHasChanged: function () {
            
            if (!this.getInitialValue() && !window.checkoutConfig.quoteData.experius_po_number) {
                return;
            }
            
            var self = this;

            clearTimeout(this.emailCheckTimeout);

            this.emailCheckTimeout = setTimeout(function () {
                self.savePonumber();
            }, self.checkDelay);
        },
        savePonumber: function () {
            
            registry.get(registry.get(this.parentName).parentName).isLoading('isLoading',true);
            
            var self = this;
            this.validateRequest();
            this.isPonumberCheckComplete = $.Deferred();
            this.checkRequest = savePonumber(this.isPonumberCheckComplete,this.getInitialValue());
            
            $.when(this.isPonumberCheckComplete).done(function (data) {
                //self.success('Saved');
            }).fail(function () {
                self.error('request failed');
            }).always(function () {
                registry.get(registry.get(self.parentName).parentName).set('isLoading',false);
            });
        },
        validateRequest: function () {
            if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                this.checkRequest.abort();
                this.checkRequest = null;
            }
        }
    })
});
