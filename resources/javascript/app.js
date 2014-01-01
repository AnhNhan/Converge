'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'router-tag-tagrouter',
], function($, _, Backbone, TagRouter) {
    var initialize = function() {
        // TODO: Write all application routers, and start them here
        TagRouter.initialize();

        // Kicking off routing
        Backbone.history.start({
            pushState: true,
            hashChange: false // Continue using URLs for older browsers
        });
    };

    return {
        initialize: initialize
    };
});
