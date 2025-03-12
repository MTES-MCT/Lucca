/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

export default function appendNewPopper(id, text, container) {
  const jasmineWrapper = document.getElementById('jasmineWrapper');

  const popper = document.createElement('div');
  popper.id = id;
  popper.className = 'popper';
  popper.textContent = text || 'popper';
  const arrow = document.createElement('div');
  arrow.className = 'popper__arrow';
  arrow.setAttribute('x-arrow', '');
  popper.appendChild(arrow);
  (container || jasmineWrapper).appendChild(popper);
  return popper;
}
