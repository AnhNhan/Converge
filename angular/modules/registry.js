'use strict';

angular.module('converge.registry', [])
// Note: This value does not get dependency tracked - inject it for yourself
.value('registerAllModules', function ($ConvergeGlobals) {
    $ConvergeGlobals.registerMenuEntry('ion-paper-airplane', '#/newsroom/', 'newsroom');
    $ConvergeGlobals.registerMenuEntry('ion-ios7-partlysunny', '#/activity/', 'activity stream');
    $ConvergeGlobals.registerMenuEntry('ion-checkmark', '#/task/', 'task listing');
    $ConvergeGlobals.registerMenuEntry('ion-navicon-round', '#/disq/', 'discussion listing');
})
;
