/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

export default function then(callback, delay = 100) {
  setTimeout(callback, jasmine.THEN_DELAY);
  jasmine.THEN_DELAY += delay;
}

beforeEach(() => (jasmine.THEN_DELAY = 0));
