'use strict';

+$(function () {
    var $window = $(window);
    var $body   = $(document.body);
    var $sideBar = $('.forum-toc-affix');
    var $header = $('#header');

    $body.scrollspy({ target: '.forum-toc-affix', offset: 300 + $header.outerHeight() })

    $sideBar.affix({
        offset: {
            top: function () {
                var sideBarMargin  = parseInt($sideBar.children(0).css('margin-top'), 10);
                return (this.top = $sideBar.offset().top - sideBarMargin - 30 + $header.outerHeight());
            }
        }
    });

    $('.forum-toc-affix .nav > li[data-toggle="popover"]').popover({
        placement: 'left',
        title: 'Preview',
        trigger: 'hover'
    });

    var fooFunc = function (e) {
        var clName = 'post-being-read';
        $('.panel-header').removeClass(clName);
        $($(this).children(0).attr('href') + ' .panel-header').addClass(clName);
        // Hm, send read-info to server?
    };
    fooFunc.call($('.forum-toc-affix .nav > li.active'), 'e');

    $('.forum-toc-affix .nav > li').on('activate.bs.scrollspy', fooFunc);

    var clickFunc = _.debounce(function () {
        $('html, body').animate({scrollTop: $($(this).attr('href')).offset().top - 280}, 800);
    }, 500, true)

    $('.forum-toc-affix a[href^="#"]').click(function (e) {
          // don't follow the link (it's a fake)
          e.preventDefault()

          // instead scroll there!
          // subtracted something since we scroll-spy with offset and have to scroll a little bit higher
          clickFunc.call(this)
    });

    // Debounce + CSS transitions = 4wsum!
    var someFunc = function (e) {
        // Yes, always re-calc these values
        // The user may have resized windows, we may have added items, etc.
        var $window = $(window);
        var $body   = $(document.body);
        var $sideBar = $('.forum-toc-affix');
        var $sidebarAffix = $('.forum-toc-affix.affix');
        var $header = $('#header');

        var visibleHeight = $window.innerHeight();
        var sidebarHeight = $sideBar.outerHeight();

        var docHeight = $(document).innerHeight();
        var currentScrollPosition = $window.scrollTop();

        if (!!(($(document).innerWidth() > 768) && $sidebarAffix.length && (sidebarHeight > visibleHeight))) {
            var offsetTop = (currentScrollPosition / (docHeight)) * ((sidebarHeight + 150) - visibleHeight + $header.outerHeight());
            $sidebarAffix.css("top", ((-offsetTop) + 30 + $header.outerHeight()) + 'px');
        } else {
            $('.forum-toc-affix').css('top', 30 + $header.outerHeight());
            $('.forum-toc-affix.affix-top').css('top', 0);
        }
    };

    someFunc();
    $window.scroll(someFunc);
});
