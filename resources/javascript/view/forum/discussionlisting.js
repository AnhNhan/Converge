'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'collection-tag-tagcollection',
    'view-forum-discussionlistingitem',
    'external-text!/tmpl-forum-discussionlist'
], function($, _, Backbone, TagCollection, DiscussionItemView, DiscussionListingTemplate) {
    var TagListView = Backbone.View.extend({
        className: 'objects-list-container forum-list-container',
        template: _.template(DiscussionListingTemplate),

        title: null,

        $list: [],
        collection: {},

        tags: null,

        initialize: function () {
            this.tags = new TagCollection;
            this.tags.fetch({reset: true});
            this.listenTo(this.collection, 'add', this.addOne);
            this.listenTo(this.collection, 'remove', this.removeOne);
            this.listenTo(this.collection, 'reset', this.addAll);

            // Add initial thingies
            this.render();
            this.addAll();
        },

        render: function () {
            this.$el.html(this.template({title: this.title}));
            return this;
        },

        addAll: function () {
            this.collection.each(this.addOne, this);
        },

        addOne: function (model, collection, options) {
            var disq = new DiscussionItemView({model: model, id: 'disq-' + model.cleanId()});
            disq.setTags(this.tags.getTagsForDiscussion(model));
            this.$('.objects-list-objects').append(disq.render().$el);
            this.$list.push(disq);
        },

        removeOne: function (model, collection, options) {
            disqView = _.find(this.$list, function (view) { return view.model.id == model.id; });

            if (disqView !== undefined) {
                disqView.remove();
            }
        }
    });

    return TagListView;
});
