'use strict';

define([
    'jquery',
    'backbone',
    'external-text!/tmpl-forum-discussionitem',
    'view-tag-tagview'
], function($, Backbone, DiscussionItemTemplate, TagView) {
    var DiscussionListingItem = Backbone.View.extend({
        className: 'objects-object-container',
        template: _.template(DiscussionItemTemplate),

        tags: {},

        initialize: function (options) {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        setTags: function (tags) {
            this.tags = tags;
        },

        render: function () {
            this.$el.html(this.template({disq: this.model}));

            var tagContainer = this.$('.taglist');
            tagContainer.html('');
            _.each(this.tags, function (tag) {
                var tagview = new TagView({model: tag});
                tagContainer.append(tagview.render().$el);
            });

            return this;
        }
    });

    return DiscussionListingItem;
});
