<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
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
				'application/epub+zip' => 'epub',
				'application/x-gzip' => 'gz',
				'application/zip' => 'zip',
				'image/bmp' => 'bmp',
				'image/gif' => 'gif',
				'image/jpeg' => 'jpg',
				'image/png' => 'png',
				'image/svg+xml' => 'svg',
				'image/tiff' => 'tif',
				'image/webp' => 'webp',
				'text/csv' => 'csv',
				'video/mp4' => 'mp4',
				'video/webm' => 'webm',
				'audio/mpeg' => 'mpeg',
				'audio/ogg' => 'ogg',
				'audio/webm' => 'weba',
			],
			'previews' => [[
				'force-size' => 0,
			]],
			'catalog' => [
				'stage' => [
					'previews' => [[
						'maxwidth' => 960,
					], [
						'maxwidth' => 1920,
					]],
				]
			],
			'product' => [
				'previews' => [[
					'maxwidth' => 240,
					'maxheight' => 320,
					'force-size' => 1,
				], [
					'maxwidth' => 480,
					'maxheight' => 640,
					'force-size' => 1,
				], [
					'maxwidth' => 960,
					'maxheight' => 1280,
					'force-size' => 1,
				], [
					'maxwidth' => 1920,
				]],
			],
		],
	],
];
