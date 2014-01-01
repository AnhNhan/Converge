'use strict';

require.config({
    shim: {
        // Not adding Underscore.js and Backbone.js here, since we have
        // patched them to call `define` already
    },
    paths: {
        // Not loading Underscore, Backbone and jQuery, since they are in
        // `libs-pck` already
        jquery: '/rsrc/js/external-jquery-1-10-2',
        underscore: '/rsrc/js/external-underscore',
        backbone: '/rsrc/js/external-backbone'
    },

    // Add this map config in addition to any baseUrl or
    // paths config you may already have in the project.
    map: {
        // '*' means all modules will get 'jquery-private'
        // for their 'jquery' dependency.
        '*': {
            'jquery': 'jquery-private',
            // text is actually external-text (because it's in that folder)
            'text':   'external-text'
        },

        // 'jquery-private' wants the real jQuery module
        // though. If this line was not here, there would
        // be an unresolvable cyclic dependency.
        'jquery-private': { 'jquery': 'jquery' }
    }
});

require([
    'app',
], function(App){
    App.initialize();
});
