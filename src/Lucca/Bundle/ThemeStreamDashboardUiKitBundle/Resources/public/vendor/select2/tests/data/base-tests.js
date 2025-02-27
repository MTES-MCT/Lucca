/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

module('Data adapters - Base');

var BaseData = require('select2/data/base');
var $ = require('jquery');
var Options = require('select2/options');

var options = new Options({});

test('current is required', function (assert) {
  var data = new BaseData($('#qunit-fixture select'), options);

  assert.throws(
    function () {
      data.current(function () {});
    },
    'current has no default implementation'
  );
});

test('query is required', function (assert) {
  var data = new BaseData($('#qunit-fixture select'), options);

  assert.throws(
    function () {
      data.query({}, function () {});
    },
    'query has no default implementation'
  );
});
