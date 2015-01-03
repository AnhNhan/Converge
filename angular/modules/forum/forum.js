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

    $scope.loadPage = function (offset) {
        $scope.loadingClass = loadingClass;
        $http.get($ConvergeConfig.apiServerUri + '/disq/?page-nr=' + offset)
            .success(function (data) {
                $scope.currentPage++;
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
                $scope.loadingClass = notLoading;
                // TODO: Tell about the error
            })
        ;
    }
    $scope.loadPage($scope.currentPage);

    $scope.loadNextPage = _.debounce(function () {
        if ($scope.loadingClass == notLoading)
        {
            $scope.loadPage($scope.currentPage);
        }
    }, 1000, true);

    var removeInfiniteScrollCallback = $InfiniteScroll.addPageBottomCallback(function () {
        $scope.loadNextPage();
    });

    $scope.$on('$destroy', function () {
        removeInfiniteScrollCallback();
    });
});

forum.controller('DiscussionPage', function ($scope, $http, $routeParams, $ConvergeGlobals, $ConvergeConfig) {
    $ConvergeGlobals.setPageTitle('Loading ' + ($routeParams.disq_label || $routeParams.disq_id) + '...');
    $scope.uriDisqId = $routeParams.disq_id;
    $scope.uriDisqLabel = $routeParams.disq_label;

    $scope.disq = $scope.disq || {};
    $scope.posts = $scope.posts || [];

    $scope.deletedClass = function (post) {
        return post.deleted ? 'post-deleted' : '';
    };

    $http.get($ConvergeConfig.apiServerUri + '/disq/' + $scope.uriDisqId)
        .success(function (data) {
            $scope.disq = data.payloads.disq;
            $scope.posts = data.payloads.posts;

            $('#disq-column > h1:first-child').slideUp(400);
        })
        .error()
    ;
});
