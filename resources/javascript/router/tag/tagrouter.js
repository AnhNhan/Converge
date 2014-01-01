'use strict';

define([
    'jquery',
    'underscore',
    'backbone'
], function($, _, Backbone) {
    var TagRouter = Backbone.Router.extend({
        routes: {
            'tag/': 'listTags',
            'tag/create': 'createTag',
            'tag/:id': 'displayTag'
        },

        listTags: function () {
            console.log("Listing tags not implemented!");
        },

        createTag: function () {
            console.log("Creating tags not implemented!");
        },

        displayTag: function (id) {
            console.log("Displaying tags not implemented!");
        }
    });

    return {
        initialize: function () {
            var tag_router = new TagRouter;
        }
    };
});
