/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/* Fix tom-select required */
document.addEventListener('DOMContentLoaded', function() {
  const forms = document.getElementsByTagName('form');

  Array.prototype.forEach.call(forms, function(form) {

    form.addEventListener('submit', function(e) {

      form.querySelectorAll('select').forEach(function(select) {
        // Check if the select is required and if it has been transformed by Tom-Select
        if (select.required && select.tomselect) {
          // Check if the select is empty
          if (!select.tomselect.getValue()) {
            select.tomselect.wrapper.classList.add('is-invalid');
            select.setCustomValidity("Ce champ est obligatoire");
            select.reportValidity()

            // If at least one required Tom-Select select is not correctly filled, prevent form submission
            e.preventDefault();

            // If the element change, reset the validity
            select.addEventListener('change', resetTomSelectValidity);

            return 2;
          }
        }
      });
    });
  });
});

function resetTomSelectValidity({ target }) {
  target.tomselect.wrapper.classList.remove('is-invalid');
  target.setCustomValidity("");

  target.removeEventListener('change', resetTomSelectValidity);
}
