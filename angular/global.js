'use strict';

var ConvergeGlobal = angular.module('converge.globals', []);

ConvergeGlobal.provider('$ConvergeGlobals', function () {
    var provider = this;

    // Note: This is unlikely to trigger any bindings, so changing directly is not too recommended
    // For some common use cases where bindings are necessary, take a look at controller nesting.
    // I do not exactly recommend either as a go-to solution though, since both will create some level of pollution
    var globals = {
        defaultTitle: 'Where one makes one with everything'
    };

    this.$get = function() {
        return globals;
    };
});

ConvergeGlobal.provider('$ConvergeTopMenuEntries', function () {
    var _entries = {
        entries: [],
        addEntry: function (href, icon, title) {
            var entry = {
                "href": href,
                "icon": icon,
                "title": title
            };

            entries += entry;
        }
    };

    this.$get = function() {
        return _entries;
    };
});
