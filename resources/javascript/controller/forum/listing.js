'use strict';

define([
    'jquery',
    'controller-base',
    'collection-forum-discussions',
    'view-forum-discussionlisting'
], function($, BaseController, DiscussionCollection, DiscussionListing) {
    var ForumListingController = BaseController.extend({
        discussions: null,
        discussionlist: null,
        initialize: function () {
            this.discussions = new DiscussionCollection;
            this.discussions.fetch({reset: true});

            this.discussionlist = new DiscussionListing({collection: this.discussions, title: 'Forum Listing'});
        },
        render: function () {
            this.$el.html(this.discussionlist.$el);
            return this;
        }
    });

    return ForumListingController;
});
