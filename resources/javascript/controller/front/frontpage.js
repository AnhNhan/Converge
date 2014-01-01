'use strict';

define([
    'external-text!/tmpl-front-frontpage'
], function(FrontPageTemplate) {
    var FrontPageController = Backbone.View.extend({
        template: FrontPageTemplate,
        initialize: function () {
            this.$el = $(this.template);
        },
        render: function () {
            return this;
        }
    });

    return FrontPageController;
});
