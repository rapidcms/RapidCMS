/*global Modernizr requirejs require console*/

(function () {
	'use strict';

	requirejs.config({
		paths: {
			'jquery': 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min'
		}
	});

	/*require(['vertical-gridder', 'jquery'], function (grid, $) {

		grid.setRhythm(24);
		$('h1').css(grid.getCSS(36));
		$('h2').css(grid.getCSS(24));
		$('h3').css(grid.getCSS(18));
		$('p, li, blockquote, q, code').css(grid.getCSS(12));

	});
	*/
}());