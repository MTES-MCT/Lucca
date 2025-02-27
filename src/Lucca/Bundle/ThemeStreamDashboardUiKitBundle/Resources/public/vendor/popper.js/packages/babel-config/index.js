/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

const env = require.resolve('babel-preset-env');
const stage2 = require.resolve('babel-preset-stage-2');
const externalHelpers = require.resolve('babel-plugin-external-helpers');

module.exports = {
  es5: {
    presets: [
      [
        env,
        {
          targets: {
            browsers: ['last 2 versions', 'ie >= 10'],
          },
          modules: false,
        },
      ],
      stage2,
    ],
    plugins: [externalHelpers],
  },
  es6: {
    presets: [stage2],
    plugins: [externalHelpers],
  },
};
