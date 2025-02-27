/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

(function ($) {
  $(document).on('ready', function () {
    var $fontController = $('.js-font-controller');

    if ($fontController.length) {
      $fontController.each(function () {
        var $this = $(this),
          $target = $($this.data('target'));

        $this.on('change', function () {
          var value = $this.val();

          $target.css('font-family', value);
        });
      });
    }
  });
})(jQuery);
