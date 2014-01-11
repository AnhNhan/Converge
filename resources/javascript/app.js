'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'router-front-frontrouter',
    'router-forum-forumrouter',
    'router-tag-tagrouter',
], function($, _, Backbone, FrontPageRoter, ForumRouter, TagRouter) {
    var initialize = function() {
        FrontPageRoter.initialize();
        ForumRouter.initialize();
        TagRouter.initialize();

        // Kicking off routing
        Backbone.history.start({
            silent: true, // Don't trigger on the initial page
            pushState: true,
            hashChange: false // Continue using URLs for older browsers
        });
    };

    return {
        initialize: initialize
    };
});
