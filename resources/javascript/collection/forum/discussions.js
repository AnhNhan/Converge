'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'model-forum-discussion',
    'collection-tag-tagcollection'
], function($, _, Backbone, DiscussionModel, TagCollection) {
    var DiscussionCollection = Backbone.Collection.extend({
        model: DiscussionModel,
        url: '/disq/',

        parse: function (response) {
            return response.payloads.discussions;
        },

        comparator: function (model) {
            return -model.get('lastActivity');
        }
    });

    return DiscussionCollection;
});
