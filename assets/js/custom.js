// Custom scripts (e.g., smooth scroll)
jQuery(document).ready(function ($) {
  // Example: Smooth scroll for anchor links
  $('a[href*="#"]').on('click', function (e) {
    e.preventDefault();
    $('html, body').animate(
      { scrollTop: $($(this).attr('href')).offset().top },
      500
    );
  });
});
