'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'collection-tag-tagcollection',
    'view-tag-taglistview'
], function($, _, Backbone, TagCollection, TagListView) {
    var TagListingController = Backbone.View.extend({
        tags: null,
        taglist: null,
        initialize: function () {
            this.tags = new TagCollection;
            this.tags.fetch({reset: true});

            this.taglist = new TagListView({collection: this.tags});
        },
        render: function () {
            this.$el.html(this.taglist.render().$el);
            return this;
        }
    });

    return TagListingController;
});
