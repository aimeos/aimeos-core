<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

return [
	'common' => [
		'media' => [
			'extensions' => [
				'application/pdf' => 'pdf',
				'application/postscript' => 'ps',
				'application/vnd.ms-excel' => 'xls',
				'application/vnd.ms-powerpoint' => 'ppt',
				'application/vnd.ms-word' => 'doc',
				'application/vnd.oasis.opendocument.graphics' => 'odg',
				'application/vnd.oasis.opendocument.presentation' => 'odp',
				'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
				'application/vnd.oasis.opendocument.text' => 'odt',
				'application/x-gzip' => 'gz',
				'application/zip' => 'zip',
				'image/gif' => 'gif',
				'image/jpeg' => 'jpg',
				'image/png' => 'png',
				'image/svg+xml' => 'svg',
				'image/tiff' => 'tif',
				'text/csv' => 'csv',
				'video/mp4' => 'mp4',
				'video/webm' => 'webm',
			],
			'previews' => [[
				'maxwidth' => 480,
				'force-size' => false,
			], [
				'maxwidth' => 960,
				'force-size' => false,
			], [
				'maxwidth' => 1920,
				'force-size' => false,
			]],
			'catalog' => [
				'stage' => [
					'previews' => [[
						'maxwidth' => 960,
						'force-size' => false,
					], [
						'maxwidth' => 1920,
						'force-size' => false,
					]],
				]
			],
			'product' => [
				'previews' => [[
					'maxwidth' => 240,
					'maxheight' => 320,
					'force-size' => false,
				], [
					'maxwidth' => 720,
					'maxheight' => 960,
					'force-size' => false,
				], [
					'maxwidth' => 2160,
					'maxheight' => 2880,
					'force-size' => false,
				]],
			],
		],
	],
];
