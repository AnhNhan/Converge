'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'view-tag-tagview'
], function($, _, Backbone, TagView) {
    var TagListView = Backbone.View.extend({
        $el: $('<div />'),
        $list: [],

        title: 'Tags',
        createButton: true,

        initialize: function () {
            this.listenTo(this.collection, 'add', this.addOne);
            this.listenTo(this.collection, 'remove', this.removeOne);
            this.listenTo(this.collection, 'reset', this.addAll);

            // Add initial thingies
            this.addAll();
        },

        render: function () {
            if (this.title) {
                this.$el.prepend($('<h1 />').text(this.title));
            }
            if (this.createButton) {
                this.$el.prepend($('<a href="/tag/create" class="btn btn-primary" style="float: right;">Create new tag!</a>'));
            }
            return this;
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
