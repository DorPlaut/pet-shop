jQuery(document).ready(function ($) {
  // Add toggle buttons for mobile
  $('.widget_product_categories .product-categories > li').each(function () {
    if ($(this).find('.children').length > 0) {
      $(this).append('<button class="category-toggle">+</button>');
    }
  });

  // Handle mobile toggle clicks
  $('.category-toggle').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $parent = $(this).parent();
    $parent.toggleClass('active');

    if ($parent.hasClass('active')) {
      $(this).text('-');
    } else {
      $(this).text('+');
    }
  });

  // Handle window resize
  $(window).resize(function () {
    if ($(window).width() > 768) {
      $('.widget_product_categories .product-categories li').removeClass(
        'active'
      );
      $('.category-toggle').text('+');
    }
  });
});
