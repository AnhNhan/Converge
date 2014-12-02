'use strict';

var Home = angular.module('converge.modules.home', [
    'converge.globals',
    'ngRoute'
]);
Home.config(function($routeProvider) {
    $routeProvider.when('/', {
        templateUrl: 'modules/home/home.html',
        controller: 'DefaultHome'
    });
});
// We only have a controller to set page title upon visiting
// We use the default page title btw - less to update when changing
// Obviously change when you want something funny
Home.controller('DefaultHome', function ($ConvergeGlobals) {
    $ConvergeGlobals.setPageTitle($ConvergeGlobals.defaultTitle);
});
