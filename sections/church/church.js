(function($) {
  'use strict';



  /*-------------------------------------------------------------------------------
   Donation Slider
  -------------------------------------------------------------------------------*/
  $(".sigma_donation-slider").slick({

    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    dots: false,
    autoplay: false,
    centerMode: true,
    centerPadding: 0,
    responsive: [
      {
        breakpoint: 767,
        settings: {
          arrows: false,
          dots: true
        }
      }
    ]
  });


  /*-------------------------------------------------------------------------------
   Progress Bar
  -------------------------------------------------------------------------------*/

  $(".sigma_progress-round").each(function() {
    var animateTo = $(this).data('to'),
      $this = $(this);
    $this.one('inview', function(event, isInView) {
      if (isInView) {
        $this.css({'stroke-dashoffset': animateTo});
      }
    });
  });


})(jQuery);
