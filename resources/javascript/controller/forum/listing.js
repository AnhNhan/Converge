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

            this.discussionlist = new DiscussionListing({collection: this.discussions});
            this.discussionlist.setTitle('Forum Listing');
            this.discussionlist.render();
        },
        render: function () {
            this.$el.html(this.discussionlist.$el);
            this.$el.prepend($('<a href="/disq/create" class="btn btn-primary" style="float: right;" data-backbone-nav>Create new discussion!</a>'));
            return this;
        }
    });

    return ForumListingController;
});
