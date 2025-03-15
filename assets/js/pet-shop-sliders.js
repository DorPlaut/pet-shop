/**
 * Pet Shop Sliders - Unified slider initialization script
 */
jQuery(document).ready(function ($) {
  // Common slider configuration options
  const sliderDefaults = {
    dots: true,
    arrows: true,
    infinite: true,
    speed: 500,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: true,
    swipeToSlide: true,
  };

  // Product slider configuration
  const productSliderConfig = {
    ...sliderDefaults,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
          arrows: false,
        },
      },
    ],
  };

  // Adoptable pets slider configuration
  const adoptionSliderConfig = {
    ...sliderDefaults,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
        },
      },
      {
        breakpoint: 576,
        settings: {
          slidesToShow: 1,
          arrows: false,
        },
      },
    ],
  };

  // Initialize product slider
  $('.product-carousel').slick(productSliderConfig);

  // Initialize adoptable pets slider
  $('.adoption-carousel').slick(adoptionSliderConfig);
});
