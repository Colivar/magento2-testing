/*global define*/
define([
    'Magento_Ui/js/form/form'
], function (Component) {
    'use strict';
    return Component.extend({
        defaults: {
            isLoading: false,
        },
        initialize: function () {
            this._super();
            return this;
        },
        initObservable: function () {
            this._super()
                .observe(['isLoading']);
            return this;
        },
        onSubmit: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('experiusPonumberForm.data.validate');
            if (!this.source.get('params.invalid')) {
                var formData = this.source.get('experiusPonumberForm');
                console.dir(formData);
            }
        }
    });
});