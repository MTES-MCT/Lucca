/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

if (('.js-sticky-sidebar')) {
  var stickySidebar = new StickySidebar('.js-sticky-sidebar', {
      topSpacing: 30,
      bottomSpacing: 30,
  });

  $('.js-sticky-sidebar a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    stickySidebar.updateSticky();
  })
}
