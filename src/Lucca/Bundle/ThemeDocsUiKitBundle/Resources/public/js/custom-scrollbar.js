/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

(function ($) {
  'use strict';

  $(document).on('ready', function () {
    // Custom Scroll
    $('.js-scrollbar').mCustomScrollbar({
      theme: 'minimal-dark',
      scrollInertia: 150
    });

    // Scroll to Active
    $('.js-scrollbar').mCustomScrollbar('scrollTo', '.js-scrollbar a.active');
  });
})(jQuery);
