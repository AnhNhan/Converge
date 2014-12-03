'use strict';

var PeopleViews = angular.module('converge.modules.people.views', []);

PeopleViews.directive('userLink', function () {
    return {
        replace: true,
        restrict: 'E',
        scope: {
            user: '='
        },
        template: '<strong><a title="@{{user.nameCanonical}}" href="#/u/{{user.nameCanonical}}" class="user-link" data-toggle="tooltip">{{user.name}}</a></strong>'
    };
});
