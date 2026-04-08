$(document).ready(function() {
    function toggleNoEndDate() {
        var endDate = $('#end_date').val();
        if (endDate) {
            $('#no_end_date').prop('checked', false);
            $('#end_date_wrapper').show();
            return; 
        }
    }
    function toggleEndDate() {
        if ($('#no_end_date').is(':checked')) {
            $('#end_date_wrapper').slideUp(200, function() {
                $('#end_date').val('');
            });
        } else {
            $('#end_date_wrapper').slideDown(200);
        }
    }

    $('#no_end_date').on('change', toggleEndDate);
    toggleEndDate();
    toggleNoEndDate();
});






// AOS.init({
//   duration: 1000
// });

$('.footer-menu-icon').on('click', function(){
     $('.menu_icon').trigger('click');
});

$('.menu_icon').on('click', function(){
    $('body').toggleClass('open');
});
$(window).scroll(function() {
    var scroll = $(window).scrollTop();
    if (scroll >= 100) {
        $("body").addClass("scroll_start");
    } else {
        $("body").removeClass("scroll_start");
    }
});


$('#hero-slider').slick({
  slidesToShow: 5,
  slidesToScroll: 1,
  lazyLoad: 'ondemand',
  autoplay: false,
  infinite: false,
  swipeToSlide: true,
  //autoplaySpeed: 0,
  speed: 1000,
  pauseOnHover: false,
  //cssEase: 'linear',
  responsive: [
    {
      breakpoint: 1366,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
     {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});



$('#featured-slider').slick({
  slidesToShow: 4,
  slidesToScroll: 4,
  autoplay: false,
  infinite: true,
  swipeToSlide: true,
  arrows: true,
  dots: false,
  speed: 1000,
  pauseOnHover: false,
  responsive: [
    {
      breakpoint: 1366,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
     {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});


$('.hero-sliderLogin-sec').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    lazyLoad: 'ondemand',
    centerMode: true,
    centerPadding: '250px', // consistent across breakpoints
    autoplay: true,
    infinite: true,
    responsive: [
        { breakpoint: 1600, settings: { centerPadding: '200px' } },
        { breakpoint: 1400, settings: { centerPadding: '150px' } },
        { breakpoint: 767,  settings: { centerPadding: '100px' } },
        { breakpoint: 480,  settings: { centerPadding: '40px' } },
    ]
});



// $('.partner-slider').slick({
//     rows: 2,
//     dots: false,
//     arrows: true,
//     infinite: true,
//     speed: 300,
//     slidesToShow: 10,
//     slidesToScroll: 1,
    // responsive: [
    // {
    //   breakpoint: 1400,
    //   settings: {
    //     slidesToShow: 8,
    //   }
    // },
    // {
    //   breakpoint: 1199,
    //   settings: {
    //     slidesToShow: 5,
    //   }
    // },
    // {
    //   breakpoint: 767,
    //   settings: {
    //     slidesToShow: 4,
    //   }
    // },
    // {
    //   breakpoint: 480,
    //   settings: {
    //     slidesToShow: 2,
    //   }
    // }
    // ]
// });

 $('.partner-slider-top').slick({
    slidesToShow: 8,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 4000,
    cssEase: 'linear',
    arrows: false,
    dots: false,
    infinite: true,
    rtl: true,
    responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 5,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 2,
      }
    }
    ]
});

$('.partner-slider-bottom').slick({
    slidesToShow: 8,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 4000,
    cssEase: 'linear',
    arrows: false,
    dots: false,
    infinite: true,
    responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 5,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 2,
      }
    }
    ]
});



$('.review-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    infinite: true,
    swipeToSlide: true,
    speed: 1000,
    responsive: [
    {
      breakpoint: 992,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '200px'
      }
    },
    {
      breakpoint: 767,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '150px'
      }
    },
    {
      breakpoint: 480,
      settings: {
        arrows: false,
        centerMode: true,
        centerPadding: '80px'
      }
    }
    ]
});


$('.paid-banner-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  dots: true,
  autoplay: false,
  infinite: true,
  swipeToSlide: true,
  speed: 1000,
  centerMode: true,
  centerPadding: '0px',
  pauseOnHover: false
});



$('.social-slider').slick({
  slidesToShow: 6,
  slidesToScroll: 1,
  autoplay: true,
  infinite: true,
  swipeToSlide: true,
  //autoplaySpeed: 0,
  speed: 1000,
  pauseOnHover: false,
  //cssEase: 'linear',
  responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 4,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 2,
      }
    }
    ]
});


$('.entries-package-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  autoplay: false,
  infinite: true,
  swipeToSlide: true,  
  speed: 1000,
  fade: true,
  pauseOnHover: false
});

$('.user-fav-slider').slick({
  slidesToShow: 3,
  slidesToScroll: 1,
  autoplay: true,
  infinite: true,
  swipeToSlide: true,  
  speed: 1000,  
  pauseOnHover: false,
  responsive: [
    {
      breakpoint: 1600,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});

$('#hero-img-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  autoplay: true,
  infinite: true,
  swipeToSlide: true,
  speed: 1000,
  fade: true,          // <-- Enable fade effect
  cssEase: 'linear',   // <-- Smooth fading
  arrows: false,
  dots: false,
  pauseOnHover: false,
  lazyLoad: 'ondemand'
});




// Tabs Heading Slider From Tablet
$(document).ready(function () {
  function initTabSlider() {
    const $tabs = $('.work-tabs-inner .nav-tabs');

    if ($(window).width() < 990) {
      if (!$tabs.hasClass('slick-initialized')) {
        $tabs.slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          dots: false,
          infinite: false,
          variableWidth: true
        });
      }
    } else {
      if ($tabs.hasClass('slick-initialized')) {
        $tabs.slick('unslick');
      }
    }
  }

  // Run on load and resize
  initTabSlider();
  $(window).on('resize', function () {
    initTabSlider();
  });
});

$(document).ready(function () {
  function initTabSlider() {
    const $tabs = $('.giveaway-sec .filter-bar .nav-tabs');

    if ($(window).width() < 990) {
      if (!$tabs.hasClass('slick-initialized')) {
        $tabs.slick({
          slidesToShow: 2,
          slidesToScroll: 1,
          arrows: true,
          dots: false,
          infinite: false,
          variableWidth: true
        });
      }
    } else {
      if ($tabs.hasClass('slick-initialized')) {
        $tabs.slick('unslick');
      }
    }
  }

  // Run on load and resize
  initTabSlider();
  $(window).on('resize', function () {
    initTabSlider();
  });
});


function initPopupSliders() {
  $('.offerModal:visible .offerTabs').each(function () {
    const $slider = $(this);

    if ($(window).width() < 990) {
      if (!$slider.hasClass('slick-initialized')) {
        $slider.slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          arrows: true,
          dots: false,
          infinite: false,
          variableWidth: true,
          swipe: true,
          swipeToSlide: true,   // smoother, natural swipe behavior
          touchThreshold: 10,   // make swiping more sensitive (lower = better)
          cssEase: 'ease-out',  // smoother easing curve
          speed: 300,           // faster response
          mobileFirst: true,
          useTransform: true
        });
      }
    } else {
      if ($slider.hasClass('slick-initialized')) {
        $slider.slick('unslick');
      }
    }
  });
}

// Re-init when modal is shown
$('.offerModal').on('shown.bs.modal', function () {
  setTimeout(initPopupSliders, 150); // small delay to ensure visibility
});


// Optional: Unslick on modal close (cleanup if re-used)
$('.offerModal').on('hidden.bs.modal', function () {
  $(this).find('.offerTabs.slick-initialized').slick('unslick');
});

// Optional: Handle window resize while modal is open
$(window).on('resize', function () {
  if ($('.offerModal:visible').length) {
    initPopupSliders();
  }
});



// var clock = $('.clock').FlipClock({
//       clockFace: 'DailyCounter',
//       countdown: true
//   });

//   var date = new Date('2025-06-27 22:00:00');
//   var dif = (date.getTime() / 10000) - ((new Date().getTime())/10000);
//   var end = Math.max(0, dif);
//   clock.setTime(end);
//   clock.start();



// var date = new Date();
// var year = date.getFullYear();

// document.getElementById('copyright-year').innerHTML = (year);


// Reward Section Slider For Mobile on Homepage
// $(document).ready(function () {
//   function initRewardSlider() {
//     if ($(window).width() <= 991) {
//       if (!$('.reward-mob-slider').hasClass('slick-initialized')) {
//         $('.reward-mob-slider').slick({
//           slidesToShow: 1,
//           slidesToScroll: 1,
//           arrows: true,
//           dots: false,
//           infinite: true,
//         });
//       }
//     } else {
//       if ($('.reward-mob-slider').hasClass('slick-initialized')) {
//         $('.reward-mob-slider').slick('unslick');
//       }
//     }
//   }

//   // Initialize on page load
//   initRewardSlider();

//   // Recheck on window resize
//   $(window).on('resize', function () {
//     initRewardSlider();
//   });
// });


// Arrows for Scrollbar on Homepage
document.addEventListener("DOMContentLoaded", function () {
  const scrollContainer = document.querySelector(".nav-tabs");
  const btnLeft = document.querySelector(".scroll-btn.left");
  const btnRight = document.querySelector(".scroll-btn.right");
  const scrollAmount = 150;

  if (btnLeft && scrollContainer) {
    btnLeft.addEventListener("click", () => {
      scrollContainer.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    });
  }

  if (btnRight && scrollContainer) {
    btnRight.addEventListener("click", () => {
      scrollContainer.scrollBy({ left: scrollAmount, behavior: "smooth" });
    });
  }
});



// $('.giveaways-slider').slick({
//   slidesToShow: 1,
//   slidesToScroll: 1,
//   autoplay: true,
//   infinite: true,
//   swipeToSlide: true,
//   speed: 1000,
//   fade: true,
//   cssEase: 'linear',
//   arrows: true,
//   dots: false,
//   pauseOnHover: false,
//   lazyLoad: 'ondemand',
//   responsive: [
//     {
//       breakpoint: 767,
//       settings: {
//         arrows: false,
//       }
//     }
//     ]
// });



$('.giveaways-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  autoplay: true,
  infinite: true,
  speed: 1000,
  fade: true,
  arrows: true,
  dots: false,
  pauseOnHover: false,
  lazyLoad: 'ondemand',
});


// Win a New Mazda Slider on Landing page
$(document).ready(function () {

  $('.main-slider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: '.thumb-slider'
  });

  $('.thumb-slider').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    asNavFor: '.main-slider',
    dots: false,
    centerMode: false,
    focusOnSelect: true,
    arrows: true,
    responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 990,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 4,
      }
    }
    ]
  });

});

// Past Winner Slider
$('.past-winner-slider').slick({
  slidesToShow: 2,
  slidesToScroll: 1,
  autoplay: true,
  infinite: true,
  swipeToSlide: true,
  speed: 1000,
  cssEase: 'linear',
  arrows: true,
  dots: false,
  pauseOnHover: false,
  lazyLoad: 'ondemand',
  responsive: [
    {
      breakpoint: 990,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});


// Review Slider Left and Right on Landing Page
 $('.review-slider-top').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 8000,
    cssEase: 'linear',
    arrows: false,
    dots: false,
    infinite: true,
    rtl: true,
    responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});
$('.review-slider-bottom').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 0,
    speed: 8000,
    cssEase: 'linear',
    arrows: false,
    dots: false,
    infinite: true,
    responsive: [
    {
      breakpoint: 1199,
      settings: {
        slidesToShow: 3,
      }
    },
    {
      breakpoint: 767,
      settings: {
        slidesToShow: 2,
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
      }
    }
    ]
});

// Win Banner Slider
$('.win-banner-slider').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  lazyLoad: 'ondemand',
  autoplay: true,
  infinite: true,
  swipeToSlide: true,
  autoplaySpeed: 6000,   // wait 6 seconds before changing
  speed: 800,            // smooth transition duration
  pauseOnHover: false,
  cssEase: 'ease'
});