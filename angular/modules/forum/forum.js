'use strict';

var forum = angular.module('converge.modules.forum', [
    'ngAnimate',
    'ngRoute',
    'converge.config',
    'converge.globals',
    'converge.modules.forum.views',
    'converge.utilities'
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

forum.controller('DiscussionListing', function ($scope, $http, $ConvergeConfig, $ConvergeGlobals, oneAfterEachOther) {
    $ConvergeGlobals.setPageTitle('Discussion Listing');
    $scope.discussions = [];
    $scope.currentPage = 0;

    var loadingClass = 'loading-progress';
    var notLoading = '';
    $scope.loadingClass = notLoading;

    $scope.pushLoad = function (offset) {
    $scope.loadingClass = loadingClass;
        $http.get($ConvergeConfig.apiServerUri + '/disq/?page-nr=' + offset)
            .success(function (data) {
                var length = data.payloads.discussions.length;
                oneAfterEachOther(40, data.payloads.discussions.length, function (i) {
                    $scope.discussions.push(data.payloads.discussions[i]);
                    if (i == length - 1) {
                        $scope.loadingClass = notLoading;
                    }
                    $scope.$apply();
                });
            })
            .error(function () {
                console.log('error');
            })
        ;
    }
    $scope.pushLoad($scope.currentPage);

    $scope.loadNextPage = function () {
        $scope.pushLoad(++$scope.currentPage);
    };
});

forum.controller('DiscussionPage', function ($scope, $http, $ConvergeConfig) {
    //
});
