(function($) {
  'use strict';

  /*-------------------------------------------------------------------------------
  One page template functionality
  -------------------------------------------------------------------------------*/
  $(".navbar-nav a").on('click', function() {
    var $this = $(this);
    $('html, body').animate({
      scrollTop: $($this.attr('href')).offset().top
    }, 2000);
  });

})(jQuery);
