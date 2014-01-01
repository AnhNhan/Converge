'use strict';

define([
    'backbone',
    'view-maincontentview',
    'controller-front-frontpage'
], function(Backbone, MainContentView, FrontPageController) {
    var FrontPageRouter = Backbone.Router.extend({
        routes: {
            '': 'frontpage'
        },

        frontpage: function () {
            MainContentView.displayController(new FrontPageController);
        }
    });

    return {
        _instance: null,
        get: function () {
            if (this._instance === null) {
                this._instance = new FrontPageRouter;
            }

            return this._instance;
        },
        initialize: function () {
            this.get();
        }
    };
});
