'use strict';

angular.module('converge',
    [
        'ngRoute',
        'converge.config',
        'converge.globals',
        'converge.modules.marketing',
        'converge.modules.home',
        'converge.modules.forum',
        'converge.registry',
        'converge.modules.test',
        'converge.toplevel'
    ]
)
.config(['$routeProvider', function($routeProvider) {
    $routeProvider.otherwise({redirectTo: '/'});
}]);

angular.module('converge.toplevel', [
    'ngRoute',
    'converge.config',
    'converge.globals'
])
.controller('ConvergePage', function ($scope, $ConvergeConfig, $ConvergeGlobals) {
    $scope.setPageTitle = $ConvergeGlobals.setPageTitle = function (title) {
        $scope.title = title;
    };

    $scope.setPageTitle($ConvergeGlobals.defaultTitle);
    $scope.installationName = $ConvergeConfig.installationName;
    $scope.pageTitleSeparator = $ConvergeConfig.pageTitleSeparator;
    $scope.thisYear = $ConvergeConfig.copyrightLatestYear;
})
.controller('ConvergeTopMenu', function ($scope, $ConvergeGlobals, registerAllModules) {
    $scope.entries = [];
    $ConvergeGlobals.registerMenuEntry = function (icon, href, text) {
        $scope.entries.push({'icon': icon, 'href': href, 'text': text});
    };

    registerAllModules($ConvergeGlobals);
})
;
