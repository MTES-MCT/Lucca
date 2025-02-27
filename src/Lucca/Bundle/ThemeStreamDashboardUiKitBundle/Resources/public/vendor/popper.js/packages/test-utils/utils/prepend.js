/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

export default function prepend(node, parent) {
  parent.insertBefore(node, parent.firstChild);
}
