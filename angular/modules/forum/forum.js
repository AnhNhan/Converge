'use strict';

var forum = angular.module('converge.modules.forum', [
    'ngAnimate',
    'ngRoute',
    'converge.config',
    'converge.globals',
    'converge.modules.infinitescroll',
    'converge.modules.forum.views',
    'converge.utilities'
]);

forum.value('pageSize', 20);

forum.config(function($routeProvider) {
    $routeProvider
        .when('/disq', {
            templateUrl: 'modules/forum/discussionlisting.html',
            controller: 'DiscussionListing'
        })
        .when('/disq/:disq_id~:disq_label', {
            templateUrl: 'modules/forum/discussionpage.html',
            controller: 'DiscussionPage'
        })
        .when('/disq/:disq_id', {
            templateUrl: 'modules/forum/discussionpage.html',
            controller: 'DiscussionPage'
        })
    ;
});

forum.controller('DiscussionListing', function ($scope, $http, $window, $ConvergeConfig, $ConvergeGlobals, $InfiniteScroll, oneAfterEachOther, pageSize) {
    $ConvergeGlobals.setPageTitle('Discussion Listing');
    $scope.discussions = $scope.discussions || [];
    $scope.currentPage = $scope.currentPage || 0;
    $scope.pageSize = $scope.pageSize || pageSize;

    var loadingClass = 'loading-progress';
    var notLoading = '';

    $scope.loadingClass = $scope.loadingClass || notLoading;

    $scope.pushLoad = function (offset) {
        $scope.loadingClass = loadingClass;
        $http.get($ConvergeConfig.apiServerUri + '/disq/?page-nr=' + offset)
            .success(function (data) {
                var length = data.payloads.discussions.length;
                oneAfterEachOther(50, data.payloads.discussions.length, function (i) {
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

    // TODO: Removing the callback once we leave the page is preferred
    $InfiniteScroll.addPageBottomCallback(function () {
        $scope.loadNextPage();
    });
});

forum.controller('DiscussionPage', function ($scope, $http, $routeParams, $ConvergeGlobals, $ConvergeConfig) {
    $ConvergeGlobals.setPageTitle('Loading ' + ($routeParams.disq_label || $routeParams.disq_id) + '...');
    $scope.uriDisqId = $routeParams.disq_id;
    $scope.uriDisqLabel = $routeParams.disq_label;
});
