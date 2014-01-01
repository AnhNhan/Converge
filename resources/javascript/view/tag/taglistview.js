'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel',
    'collection-tag-tagcollection',
    'view-tag-tagview'
], function($, _, Backbone, TagModel, TagCollection, TagView) {
    var TagListView = Backbone.View.extend({
        $el: $('<div />'),
        $list: [],

        initialize: function () {
            this.listenTo(this.collection, 'add', this.addOne);
            this.listenTo(this.collection, 'remove', this.removeOne);
            this.listenTo(this.collection, 'reset', this.addAll);

            // Add initial thingies
            this.addAll();
        },

        addAll: function () {
            this.collection.each(this.addOne, this);
        },

        addOne: function (model, collection, options) {
            var tag = new TagView({model: model, id: 'tag-' + model.cleanId()});
            this.$el.append(tag.render().$el);
            this.$list.push(tag);
        },

        removeOne: function (model, collection, options) {
            tagview = _.find(this.$list, function (view) { return view.model.id == model.id; });

            if (tagview !== undefined) {
                tagview.remove();
            }
        }
    });

    return TagListView;
});
