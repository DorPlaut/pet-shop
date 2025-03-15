jQuery(function ($) {
  // Variables to store state
  var isAjaxRunning = false;

  // Initialize on page load
  initializeCartHandlers();

  /**
   * Initialize all cart-related handlers
   */
  function initializeCartHandlers() {
    // Replace standard add to cart with AJAX version for archive/loop pages
    replaceArchiveAddToCart();

    // Listen for added_to_cart event
    $(document.body).on('added_to_cart', handleAddedToCart);

    // Force fragments refresh on page load
    refreshCartFragments();

    // Also refresh fragments when switching tabs or returning to the page
    $(window).on('focus', refreshCartFragments);
  }

  /**
   * Replace standard add to cart buttons with AJAX versions
   */
  function replaceArchiveAddToCart() {
    // For products in loops (archives, sliders, etc.)
    $(
      '.product-type-simple:not(.product-type-external) .add_to_cart_button:not(.ajax_add_to_cart)'
    ).each(function () {
      var $button = $(this);
      var productId =
        $button.data('product_id') || $button.attr('data-product_id');

      if (productId) {
        $button.addClass('ajax_add_to_cart');
        $button.attr('data-product_id', productId);
        $button.attr('data-quantity', $button.attr('data-quantity') || 1);
      }
    });

    // For products with attributes, we need custom handling
    $(
      '.product-type-variable .add_to_cart_button, .product.outofstock .add_to_cart_button'
    )
      .off('click.custom')
      .on('click.custom', function (e) {
        var $button = $(this);
        var href = $button.attr('href');

        if (href && href.indexOf('add-to-cart') !== -1) {
          e.preventDefault();
          window.location.href = href;
        }
      });
  }

  /**
   * Handle added_to_cart event
   */
  function handleAddedToCart(event, fragments, cart_hash, $button) {
    // Update cart counter with the new count
    updateCartCounter(fragments);

    // Show success message
    showAddToCartNotification($button);
  }

  /**
   * Show a notification when product is added to cart
   */
  function showAddToCartNotification($button) {
    var productName = $button
      .closest('.product')
      .find('.woocommerce-loop-product__title')
      .text();
    if (!productName) {
      productName = $button
        .closest('.product-content')
        .find('h2, h3')
        .first()
        .text();
    }
    if (!productName) {
      productName = 'Product';
    }

    // Create notification element if it doesn't exist
    if ($('#cart-notification').length === 0) {
      $('body').append('<div id="cart-notification"></div>');
    }

    // Show notification
    $('#cart-notification')
      .html(
        '<div class="cart-notification-inner"><span class="dashicons dashicons-yes"></span> ' +
          productName +
          ' added to cart! <a href="' +
          wc_add_to_cart_params.cart_url +
          '">View Cart</a></div>'
      )
      .addClass('show');

    // Hide notification after 3 seconds
    setTimeout(function () {
      $('#cart-notification').removeClass('show');
    }, 3000);
  }

  /**
   * Update all cart counters on the page
   */
  function updateCartCounter(fragments) {
    // If fragments is not provided, try to get it
    if (!fragments) {
      refreshCartFragments();
      return;
    }

    // Extract the cart count from fragments
    var cartCount = 0;

    // First try to get count from the refreshed mini-cart
    if (fragments['div.widget_shopping_cart_content']) {
      var $content = $(fragments['div.widget_shopping_cart_content']);
      var $countElement = $content.find('.cart-contents-count');

      if ($countElement.length) {
        cartCount = $countElement.text();
      } else {
        // If no specific count element, try to count items
        cartCount = $content.find('.mini_cart_item').length;
      }
    }

    // If we have a count, update all count elements
    $('.cart-contents-count ').text(cartCount);

    // Also update any custom cart count elements
    $('.custom-cart-contents-count').text(cartCount);

    // If count is 0, you might want to hide the counter
    if (parseInt(cartCount) === 0) {
      $('.cart-contents-count , .custom-cart-contents-count').addClass('empty');
    } else {
      $('.cart-contents-count , .custom-cart-contents-count').removeClass(
        'empty'
      );
    }
  }

  /**
   * Force refresh cart fragments
   */
  function refreshCartFragments() {
    if (isAjaxRunning || typeof wc_cart_fragments_params === 'undefined') {
      return;
    }

    isAjaxRunning = true;

    $.ajax({
      url: wc_cart_fragments_params.wc_ajax_url
        .toString()
        .replace('%%endpoint%%', 'get_refreshed_fragments'),
      type: 'POST',
      data: {
        time: new Date().getTime(),
      },
      success: function (data) {
        if (data && data.fragments) {
          // Update fragments
          $.each(data.fragments, function (key, value) {
            $(key).replaceWith(value);
          });

          // Update cart counter
          updateCartCounter(data.fragments);

          // Store fragment data
          if (data.cart_hash) {
            sessionStorage.setItem(
              wc_cart_fragments_params.fragment_name,
              JSON.stringify(data.fragments)
            );
            localStorage.setItem(
              wc_cart_fragments_params.cart_hash_key,
              data.cart_hash
            );
            sessionStorage.setItem(
              wc_cart_fragments_params.cart_hash_key,
              data.cart_hash
            );
          }

          $(document.body).trigger('wc_fragments_refreshed');
        }

        isAjaxRunning = false;
      },
      error: function () {
        isAjaxRunning = false;
      },
    });
  }

  /**
   * Add AJAX handling to archive page add to cart buttons
   */
  $(document.body).on(
    'click',
    '.archive .ajax_add_to_cart, .products .ajax_add_to_cart, .product-slider .ajax_add_to_cart',
    function (e) {
      var $button = $(this);

      // If the button already has loading class, don't proceed
      if ($button.hasClass('loading')) {
        return;
      }

      var productId = $button.data('product_id');
      var quantity = $button.data('quantity') || 1;

      // Only proceed if we have a product ID
      if (!productId) {
        return;
      }

      e.preventDefault();

      // Add loading class
      $button.addClass('loading');

      // Trigger AJAX add to cart
      $.ajax({
        type: 'POST',
        url: wc_add_to_cart_params.wc_ajax_url.replace(
          '%%endpoint%%',
          'add_to_cart'
        ),
        data: {
          product_id: productId,
          quantity: quantity,
        },
        success: function (response) {
          if (!response) {
            return;
          }

          if (response.error && response.product_url) {
            window.location = response.product_url;
            return;
          }

          // Trigger event so themes can refresh other areas
          $(document.body).trigger('added_to_cart', [
            response.fragments,
            response.cart_hash,
            $button,
          ]);

          // Remove loading class
          $button.removeClass('loading').addClass('added');
        },
        error: function () {
          $button.removeClass('loading');
          console.log('Ajax error: Failed to add product to cart');
        },
      });
    }
  );
});
