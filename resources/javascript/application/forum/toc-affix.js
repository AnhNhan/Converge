(function ($) {
    var $window = $(window);
    var $body   = $(document.body);

    $body.scrollspy({
      target: '.forum-toc-affix'
    });
    $window.on('load', function () {
      $body.scrollspy('refresh')
    });

    // back to top
    setTimeout(function () {
      var $sideBar = $('.forum-toc-affix')

      $sideBar.affix({
        offset: {
          top: function () {
            var sideBarMargin  = parseInt($sideBar.children(0).css('margin-top'), 10)
            return (this.top = $sideBar.offset().top - sideBarMargin - 30)
          }
        , bottom: function () {
            return (this.bottom = 60)
          }
        }
      })
    }, 100);
  })(jQuery);
