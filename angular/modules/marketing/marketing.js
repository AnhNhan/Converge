'use strict';

var Marketing = angular.module('converge.modules.marketing', [
    'ngRoute'
]);

Marketing.config(['$routeProvider', function ($routeProvider) {
    $routeProvider
        .when('/marketing/landing1', {
            templateUrl: 'modules/marketing/landing1.html'
        })
    ;
}]);
