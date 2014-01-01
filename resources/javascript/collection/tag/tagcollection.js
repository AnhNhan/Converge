'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel'
], function($, _, Backbone, TagModel) {
    var TagsCollection = Backbone.Collection.extend({
        model: TagModel,
        url: '/tag/',

        parse: function (response) {
            return response.payloads.tags;
        },

        comparator: 'displayOrder',

        color: function (color) {
            return this.filter(function (tag) {
                return tag.get('color') == color;
            });
        }
    });

    return TagsCollection;
});
