#!/bin/bash

#
# Copyright (c) 2025. Numeric Wave
#
# Affero General Public License (AGPL) v3
#
# For more information, please refer to the LICENSE file at the root of the project.
#

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

# Fix permissions for the /srv/docs directory
chown www-data:www-data /srv/docs

exec docker-php-entrypoint "$@"
