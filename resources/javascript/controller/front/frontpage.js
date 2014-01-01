'use strict';

define([
    'controller-base',
    'external-text!/tmpl-front-frontpage'
], function(BaseController, FrontPageTemplate) {
    var FrontPageController = BaseController.extend({
        template: FrontPageTemplate,
        initialize: function () {
            this.$el = $(this.template);
        }
    });

    return FrontPageController;
});
