'use strict';

var config = angular.module('converge.config', []);

config.provider('$ConvergeConfig', function () {
    var provider = this;

    var config = {
        copyrightLatestYear: 2015,
        apiServerUri: '//api.converge.dev',
        // E.g. Converge, Bob's private task tracker
        installationName: 'Anh Nhan\'s Little Place',
        // E.g. `-` yields `Foo - Converge`
        pageTitleSeparator: '-'
    };

    this.$get = function() {
        return config;
    };
});
