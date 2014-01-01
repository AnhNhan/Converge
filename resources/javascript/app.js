'use strict';

define([
    'jquery',
    'underscore',
    'backbone',
    'router-front-frontrouter',
    'router-tag-tagrouter',
], function($, _, Backbone, FrontPageRoter, TagRouter) {
    var initialize = function() {
        // TODO: Write all application routers, and start them here
        FrontPageRoter.initialize();
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
