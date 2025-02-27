/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

define( [
	"./cssExpand"
], function( cssExpand ) {
	"use strict";

	return new RegExp( cssExpand.join( "|" ), "i" );
} );
