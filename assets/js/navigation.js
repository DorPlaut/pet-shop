/**
Mobile navigation functionality with complete resize support
 */
document.addEventListener('DOMContentLoaded', function () {
  // Mobile menu toggle
  var isMobile = window.innerWidth <= 768;

  // HANDLE MAIN GATEGORIES MENU BUTTON
  var menuToggle = document.querySelector('.category-menu-toggle');
  var mainNav = document.querySelector('.main-navigation');
  // handke main nav class
  const handleMenuClass = () => {
    if (isMobile) {
      mainNav.classList.add('mobile-nav-menu');
    } else {
      mainNav.classList.remove('mobile-nav-menu');
    }
  };

  // Toggle menu function
  function setupMenuToggle() {
    if (menuToggle && mainNav) {
      menuToggle.addEventListener('click', function () {
        this.classList.toggle('active');
        mainNav.classList.toggle('expanded');

        // Save preference
        if (mainNav.classList.contains('expanded')) {
          localStorage.setItem('categoryMenuExpanded', 'true');
        } else {
          localStorage.setItem('categoryMenuExpanded', 'false');
        }
      });
    }
  }

  // HANDELE INSIDE CATEGORY BUTTONS

  // Add category toggle buttons for mobile
  function addCategoryToggles() {
    // Add toggle buttons to categories with children
    var categoryItems = document.querySelectorAll(
      '.widget_product_categories .product-categories > li'
    );

    for (var i = 0; i < categoryItems.length; i++) {
      var item = categoryItems[i];
      var hasChildren = item.querySelector('.children');
      var existingToggle = item.querySelector('.category-toggle');

      if (hasChildren && !existingToggle) {
        // Create toggle button
        var toggleBtn = document.createElement('button');
        toggleBtn.className = 'category-toggle mobile-only';
        toggleBtn.innerHTML = '+';
        toggleBtn.setAttribute('aria-label', 'Toggle submenu');

        // Add button to the category
        item.appendChild(toggleBtn);

        // Add click handler
        toggleBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          var parent = this.parentNode;
          parent.classList.toggle('active');

          if (parent.classList.contains('active')) {
            this.innerHTML = '-';
          } else {
            this.innerHTML = '+';
          }
        });
      }
    }
  }

  // END

  // Handle layout changes
  // Function to handle layout changes based on window width
  function handleLayoutChange() {
    var wasMobile = isMobile;
    isMobile = window.innerWidth <= 768;
    // Layout changed
    if (wasMobile !== isMobile) {
      // handle menu class
      handleMenuClass();
    }
  }

  // Handle window resize
  window.addEventListener('resize', function () {
    handleLayoutChange();
  });

  // Run initial setup
  // add category toggles
  addCategoryToggles();
  // handle menu class
  handleMenuClass();
  // setup menu toggle
  setupMenuToggle();

  if (isMobile) {
    var savedPreference = localStorage.getItem('categoryMenuExpanded');
    if (savedPreference === 'true') {
      mainNav.classList.add('expanded');
      menuToggle.classList.add('active');
    }
  }
});
