'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'model-tag-tagmodel'
], function($, _, Backbone, TagModel) {
    var TagView = Backbone.View.extend({
        className: 'tag-object',
        template: _.template($('<div><span></span></div>').html()),

        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
            this.$('span').text(this.model.get('label'));

            if (this.model.get('color')) {
                this.$el.addClass('tag-color-' + this.model.escape('color'));
            }

            return this;
        }
    });

    return TagView;
});
