/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

import makeElement from './makeElement';

/**
 * Create an element that's connected to the DOM.
 */
export default function makeConnectedElement() {
  const jasmineWrapper = document.getElementById('jasmineWrapper');
  return jasmineWrapper.appendChild(makeElement());
}
