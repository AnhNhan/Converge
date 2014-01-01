'use strict';

define([
    'jquery',
    'backbone'
], function($, Backbone) {
    var MainContentView = Backbone.View.extend({
        currentController: null,
        displayController: function (controller) {
            if (this.currentController !== undefined && this.currentController !== null) {
                this.currentController.destroy();
            }

            this.currentController = controller;
            this.render();
        },
        render: function () {
            $('.content').html(this.currentController.render().$el);
        }
    });

    return new MainContentView;
});
