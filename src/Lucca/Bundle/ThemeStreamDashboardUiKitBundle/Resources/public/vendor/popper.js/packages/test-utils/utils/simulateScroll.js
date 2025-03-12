/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

export default function simulateScroll(
  element,
  { scrollTop, scrollLeft, delay }
) {
  const scrollingElement = element === document.body
    ? document.scrollingElement || document.documentElement
    : element;

  const applyScroll = () => {
    if (scrollTop !== undefined) {
      scrollingElement.scrollTop = scrollTop;
    }
    if (scrollLeft !== undefined) {
      scrollingElement.scrollLeft = scrollLeft;
    }
  };

  if (delay !== undefined) {
    setTimeout(applyScroll, delay);
  } else {
    applyScroll();
  }
}
