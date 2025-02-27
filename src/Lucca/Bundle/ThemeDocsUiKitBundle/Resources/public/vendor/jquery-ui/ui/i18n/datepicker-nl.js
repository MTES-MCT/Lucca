/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/* Dutch (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Mathias Bynens <http://mathiasbynens.be/> */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.nl = {
	closeText: "Sluiten",
	prevText: "←",
	nextText: "→",
	currentText: "Vandaag",
	monthNames: [ "januari", "februari", "maart", "april", "mei", "juni",
	"juli", "augustus", "september", "oktober", "november", "december" ],
	monthNamesShort: [ "jan", "feb", "mrt", "apr", "mei", "jun",
	"jul", "aug", "sep", "okt", "nov", "dec" ],
	dayNames: [ "zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag" ],
	dayNamesShort: [ "zon", "maa", "din", "woe", "don", "vri", "zat" ],
	dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
	weekHeader: "Wk",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.nl );

return datepicker.regional.nl;

} ) );
