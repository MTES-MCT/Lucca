/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

export default function appendNewRef(id, text, container) {
  const jasmineWrapper = document.getElementById('jasmineWrapper');

  const ref = document.createElement('div');
  ref.id = id;
  ref.className = 'ref';
  ref.textContent = text || 'reference';
  (container || jasmineWrapper).appendChild(ref);
  return ref;
}
