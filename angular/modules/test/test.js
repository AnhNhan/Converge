'use strict';

var TestModule = angular.module('converge.modules.test', [
    'ngRoute'
]);

TestModule.config(function ($routeProvider) {
    $routeProvider
        .when('/test/content-editable', {
            templateUrl: 'modules/test/content-editable.html'
        })
    ;
});

TestModule.directive("contenteditable", function($sce) {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {
            function read() {
                ngModel.$setViewValue($sce.trustAsHtml(element.html()));
            }

            ngModel.$render = function() {
                element.html(ngModel.$viewValue || "");
            };

            element.bind("blur keyup change", function() {
                scope.$apply(read);
            });
        }
    };
});
