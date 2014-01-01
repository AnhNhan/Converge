'use strict';

require.config({
    shim: {
        // Not adding Underscore.js and Backbone.js here, since we have
        // patched them to call `define` already
    },
    paths: {
    }
});

require([
    'app',
], function(App){
    App.initialize();
});
