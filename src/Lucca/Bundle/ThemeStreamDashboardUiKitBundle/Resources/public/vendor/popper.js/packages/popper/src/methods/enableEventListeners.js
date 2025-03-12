/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

import setupEventListeners from '../utils/setupEventListeners';

/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */
export default function enableEventListeners() {
  if (!this.state.eventsEnabled) {
    this.state = setupEventListeners(
      this.reference,
      this.options,
      this.state,
      this.scheduleUpdate
    );
  }
}
