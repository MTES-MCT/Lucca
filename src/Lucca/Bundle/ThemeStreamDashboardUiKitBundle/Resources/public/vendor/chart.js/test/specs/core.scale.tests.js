/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

describe('Core.scale', function() {
	describe('auto', jasmine.specsFromFixtures('core.scale'));

	it('should provide default scale label options', function() {
		expect(Chart.defaults.scale.scaleLabel).toEqual({
			// display property
			display: false,

			// actual label
			labelString: '',

			// actual label
			lineHeight: 1.2,

			// top/bottom padding
			padding: {
				top: 4,
				bottom: 4
			}
		});
	});
});
