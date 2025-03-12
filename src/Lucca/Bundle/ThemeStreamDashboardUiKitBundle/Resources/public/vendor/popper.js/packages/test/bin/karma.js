/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

#!/usr/bin/env node
const path = require('path');
const { Server, config } = require('karma');

const karmaConfig = config.parseConfig(
  path.resolve(__dirname, '../karma.conf.js')
);

const server = new Server(karmaConfig, exitCode => {
  console.log('Karma has exited with ' + exitCode);
  process.exit(exitCode);
});

server.start();
