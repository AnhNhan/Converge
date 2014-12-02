'use strict';

var forum = angular.module('converge.modules.forum', [
    'ngRoute',
    'converge.config',
    'converge.globals'
]);

forum.config(function($routeProvider) {
    $routeProvider
        .when('/disq', {
            templateUrl: 'modules/forum/discussionlisting.html',
            controller: 'DiscussionListing'
        })
        .when('/disq/:disq_id', {
            templateUrl: 'modules/forum/discussionpage.html',
            controller: 'DiscussionPage'
        })
    ;
});

forum.controller('DiscussionListing', function ($scope, $http, $ConvergeConfig, $ConvergeGlobals) {
    $ConvergeGlobals.setPageTitle('Discussion Listing');
    $scope.discussions = [];
    $scope.currentPage = 1;

    $scope.pushLoad = function (offset) {
        $http.get($ConvergeConfig.apiServerUri + '/disq/?page-nr=' + offset)
            .success(function (data) {
                for (var i = data.payloads.discussions.length - 1; i >= 0; i--) {
                    $scope.discussions.push(data.payloads.discussions[i]);
                };
            })
            .error(function (e) {
                console.log('error');
            })
        ;
    }
    $scope.pushLoad($scope.currentPage);
});

forum.controller('DiscussionPage', function ($scope, $http, $ConvergeConfig) {
    //
});
