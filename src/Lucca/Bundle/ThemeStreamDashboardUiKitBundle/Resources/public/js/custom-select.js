/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

(function ($) {
  $(document).on('ready', function () {
    var $fontController = $('.js-custom-select');

    if ($fontController.length) {
      $fontController.each(function () {
        var $this = $(this),
          placeholder = $this.data('placeholder');

        $this.select2({
          placeholder: placeholder,
          containerCssClass: 'u-select-line',
          dropdownCssClass: 'u-select-line__dropdown',
          minimumResultsForSearch: Infinity
        });
      });
    }
  });
})(jQuery);
