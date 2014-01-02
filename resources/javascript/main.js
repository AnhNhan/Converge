'use strict';

require.config({
    shim: {
        // Not adding Underscore.js and Backbone.js here, since we have
        // patched them to call `define` already
    },
    paths: {
        // Not adding jQuery, Underscore.js and Backbone.js here, since we
        // already have them loaded in `libs-pck` already, and have our own
        // loaders ready
    }
});

require([
    'app',
], function(App){
    App.initialize();
});
