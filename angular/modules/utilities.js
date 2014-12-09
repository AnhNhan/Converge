'use strict';

var Utilities = angular.module('converge.utilities', []);

Utilities.filter('cleanId', function () {
    return function (input) {
        return input.replace(/^([A-Z]+-)*/, '');
    };
});

Utilities.filter('encodeURIComponent', function ($window) {
    return $window.encodeURIComponent;
});

Utilities.value('oneAfterEachOther', function (time, times, action) {
    var i = 0;
    var timeoutCB = function () {
        if (i < times) {
            action(i++);
            setTimeout(timeoutCB, time);
        }
    };
    timeoutCB();
});
