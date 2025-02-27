/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

import makeConnectedElement from './makeConnectedElement';

/**
 * Create a scrollable element that's connected to the DOM.
 */
export default function makeConnectedScrollElement() {
  const elem = makeConnectedElement();
  elem.style.overflow = 'scroll';
  return elem;
}
