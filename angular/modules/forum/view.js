'use strict';

var ForumViews = angular.module('converge.modules.forum.views', [
    'converge.modules.people.views'
]);

ForumViews.filter('extractUser', function () {
    return function (disq) {
        return {
            name: disq.authorName,
            nameCanonical: disq.authorNameCanonical
        };
    };
});

ForumViews.directive('forumListing', function () {
    return {
        restrict: 'E',
        replace: true,
        transclude: true,
        template: '<div class="objects-list-container forum-list-container"><div class="objects-list-objects" ng-transclude></div></div>'
    };
});

ForumViews.directive('forumListingEntry', function () {
    return {
        restrict: 'E',
        replace: true,
        templateUrl: 'modules/forum/discussionlistingentry.html'
    };
});
