
+ $(function () {
  var $window = $(window);
  var $body   = $(document.body);
  var $sideBar = $('.forum-toc-affix');

  $body.scrollspy({ target: '.forum-toc-affix', offset: 300 })

  $sideBar.affix({
    offset: {
      top: function () {
        var sideBarMargin  = parseInt($sideBar.children(0).css('margin-top'), 10);
        return (this.top = $sideBar.offset().top - sideBarMargin - 30);
      }
    }
  });

  var fooFunc = _.debounce(function (e) {
    var clName = 'post-being-read';
    $('.panel-midriff').removeClass(clName);
    $($(this).children(0).attr('href') + ' .panel-midriff').addClass(clName);
    // Hm, send read-info to server?
  }, 300);
  fooFunc.call($('.forum-toc-affix .nav > li.active'), 'e');

  $('.forum-toc-affix .nav > li').on('activate.bs.scrollspy', fooFunc);

  $('.forum-toc-affix a[href^="#"]').click(function (e) {
      e.preventDefault()
  });

  // Debounce + CSS transitions = 4wsum!
  var someFunc = _.debounce(function (e) {
      // Yes, always re-calc these values
      // The user may have resized windows, we may have added items, etc.
      var $window = $(window);
      var $body   = $(document.body);
      var $sideBar = $('.forum-toc-affix');
      var $sidebarAffix = $('.forum-toc-affix.affix');

      var visibleHeight = $window.innerHeight();
      var sidebarHeight = $sideBar.outerHeight();

      var docHeight = $(document).innerHeight();
      var currentScrollPosition = $window.scrollTop();

      if (!!(($(document).innerWidth() > 768) && $sidebarAffix.length && (sidebarHeight > visibleHeight))) {
        var offsetTop = (currentScrollPosition / (docHeight)) * ((sidebarHeight + 150) - visibleHeight);
        $sidebarAffix.css("top", ((-offsetTop) + 30) + 'px');
      } else {
        $('.forum-toc-affix').css('top', 0);
      }
    }, 300);

  someFunc();
  $window.scroll(someFunc);
});
