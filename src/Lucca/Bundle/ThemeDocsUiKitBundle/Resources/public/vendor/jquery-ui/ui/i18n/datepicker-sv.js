/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/* Swedish initialisation for the jQuery UI date picker plugin. */
/* Written by Anders Ekdahl ( anders@nomadiz.se). */
( function( factory ) {
	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
}( function( datepicker ) {

datepicker.regional.sv = {
	closeText: "Stäng",
	prevText: "&#xAB;Förra",
	nextText: "Nästa&#xBB;",
	currentText: "Idag",
	monthNames: [ "Januari","Februari","Mars","April","Maj","Juni",
	"Juli","Augusti","September","Oktober","November","December" ],
	monthNamesShort: [ "Jan","Feb","Mar","Apr","Maj","Jun",
	"Jul","Aug","Sep","Okt","Nov","Dec" ],
	dayNamesShort: [ "Sön","Mån","Tis","Ons","Tor","Fre","Lör" ],
	dayNames: [ "Söndag","Måndag","Tisdag","Onsdag","Torsdag","Fredag","Lördag" ],
	dayNamesMin: [ "Sö","Må","Ti","On","To","Fr","Lö" ],
	weekHeader: "Ve",
	dateFormat: "yy-mm-dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sv );

return datepicker.regional.sv;

} ) );
