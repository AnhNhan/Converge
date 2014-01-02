'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'view-maincontentview',
    'controller-forum-listing'
], function($, _, Backbone, MainContentView, ForumListingController) {
    var ForumRouter = Backbone.Router.extend({
        routes: {
            'disq/': 'listDiscussions',
            'disq/create': 'createDiscussion',
            'disq/:id': 'displayDiscussion'
        },

        listDiscussions: function () {
            MainContentView.displayController(new ForumListingController);
        },

        createDiscussion: function () {
            console.log("Creating tags not implemented!");
        },

        displayDiscussion: function (id) {
            console.log("Displaying tags not implemented!");
        }
    });

    return {
        _instance: null,
        get: function () {
            if (this._instance === null) {
                this._instance = new ForumRouter;
            }

            return this._instance;
        },
        initialize: function () {
            this.get();
        }
    };
});
