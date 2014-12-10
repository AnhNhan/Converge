'use strict';

var TagViews = angular.module('converge.modules.tag.views', []);

TagViews.directive('tag-object', function () {
    return {
        replace: true,
        restrict: 'E',
        scope: {
            tag: '='
        },
        template: '<span class="tag-object tag-color-{{tag.color}}">{{tag.label}}</span>'
    };
});

TagViews.directive('tag-link', function () {
    return {
        replace: true,
        restrict: 'E',
        scope: {
            tag: '='
        },
        template: '<a href="#tag/{{tag.uid}}"><tag-object tag="{{tag}}" /></a>'
    };
});
