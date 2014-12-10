'use strict';

var InfiniteScroll = angular.module('converge.modules.infinitescroll', []);

var pageBottomCallbacks = [];

var $InfiniteScroll = {
    addPageBottomCallback: function (callback) {
        pageBottomCallbacks.push(callback);

        // TODO: Test this function. I don't remember JavaScript well enough to
        //       know whether `cb != callback` actually works.
        // Consideration: Have the callback remove only this specific instance,
        //                or have it remove *all* instances?
        return function () {
            pageBottomCallbacks = _.filter(pageBottomCallbacks, function (cb) {
                return cb != callback;
            });
        };
    }
};

InfiniteScroll.value('$InfiniteScroll', $InfiniteScroll);

var strgrsdeds = _.debounce(function (e) {
    // Yes, always re-calc these values
    // The user may have resized windows, we may have added items, etc.
    var $window = $(window);

    var visibleHeight = $window.innerHeight();
    var docHeight = $(document).innerHeight();

    var currentScrollPosition = $window.scrollTop();
    var scrollHeight = docHeight - visibleHeight;

    if ((scrollHeight - currentScrollPosition) < 100) {
        for (var i = pageBottomCallbacks.length - 1; i >= 0; i--) {
            pageBottomCallbacks[i]();
        };
    }
}, 300);

$(window).scroll(strgrsdeds);
