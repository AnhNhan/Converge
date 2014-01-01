'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'view-maincontentview',
    'controller-tag-listing'
], function($, _, Backbone, MainContentView, TagListingController) {
    var TagRouter = Backbone.Router.extend({
        routes: {
            'tag/': 'listTags',
            'tag/create': 'createTag',
            'tag/:id': 'displayTag'
        },

        listTags: function () {
            MainContentView.displayController(new TagListingController);
        },

        createTag: function () {
            console.log("Creating tags not implemented!");
        },

        displayTag: function (id) {
            console.log("Displaying tags not implemented!");
        }
    });

    return {
        _instance: null,
        get: function () {
            if (this._instance === null) {
                this._instance = new TagRouter;
            }

            return this._instance;
        },
        initialize: function () {
            this.get();
        }
    };
});
