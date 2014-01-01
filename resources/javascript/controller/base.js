'use strict';

define([
    'backbone',
], function(Backbone) {
    var BaseController = Backbone.View.extend({
        render: function () {
            return this;
        },
        cleanup: function () {
            // <empty>
        }
    });

    return BaseController;
});
