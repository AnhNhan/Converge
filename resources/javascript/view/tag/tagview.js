'use strict';

define([
    'jquery',
    'backbone',
    'external-text!/tmpl-tag-tagview'
], function($, Backbone, TagViewTemplate) {
    var TagView = Backbone.View.extend({
        className: 'tag-object',
        template: _.template(TagViewTemplate),

        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            this.$el.html(this.template(this.model.toJSON()));

            if (this.model.get('color')) {
                this.$el.addClass('tag-color-' + this.model.escape('color'));
            }

            return this;
        }
    });

    return TagView;
});
