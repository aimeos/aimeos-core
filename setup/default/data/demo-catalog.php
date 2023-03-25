<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org], 2015-2023
 */

return [
	'catalog.code' => 'home', 'catalog.label' => 'Home', 'catalog.config' => ['css-class' => 'megamenu'],
	'text' => [
		[
			'label' => 'Demo name/de', 'content' => 'Start',
			'type' => 'name', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo url/de', 'content' => 'Start',
			'type' => 'url', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo short/de',
			'content' => 'Hohe Qualität, niedrige Preise!',
			'type' => 'short', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo short/en',
			'content' => 'High quality, low prices!',
			'type' => 'short', 'languageid' => 'en', 'status' => 1,
			'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo long/de',
			'content' => 'Hier finden Sie eine fantastische Auswahl zu günstigesten Preisen!',
			'type' => 'long', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo long/en',
			'content' => 'Find a fantastic selection of products for the cheapest price!',
			'type' => 'long', 'languageid' => 'en', 'status' => 1,
			'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
		[
			'label' => 'Demo meta-description',
			'content' => 'Meta descriptions are important because they are shown in the search engine result page',
			'type' => 'meta-description', 'languageid' => null, 'status' => 1,
			'list-type' => 'default', 'list-position' => 5, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		],
	],
	'catalog' => [[
		'catalog.code' => 'demo-best', 'catalog.label' => 'Best sellers',
		'text' => [
			[
				'label' => 'Best seller kurz',
				'content' => '<p>Große Auswahl an TOP Sellern<br /><strong>BESTE Preise garantiert</strong></p>',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Best seller short',
				'content' => '<p>LARGE selection of TOP sellers<br /><strong>BEST prices guaranteed</strong></p>',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Demo meta-description',
				'content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'type' => 'meta-description', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
		'media' => [
			[
				'label' => 'Demo: Best seller stage', 'mimetype' => 'image/webp',
				'url' => 'https://aimeos.org/media/default/main-banner-1-big.webp',
				'preview' => [
					480 => 'https://aimeos.org/media/default/main-banner-1-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-1-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-1-big.webp',
				],
				'type' => 'stage', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Demo: Best seller menu', 'mimetype' => 'image/webp',
				'url' => 'https://aimeos.org/media/default/product_01_A-low.webp',
				'preview' => [
					240 => 'https://aimeos.org/media/default/product_01_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_01_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_01_A-big.webp',
				],
				'type' => 'menu', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
		'catalog' => [[
			'catalog.code' => 'demo-best-women', 'catalog.label' => 'Women',
			'catalog' => [[
				'catalog.code' => 'demo-best-women-shirt', 'catalog.label' => 'Shirts'
			], [
				'catalog.code' => 'demo-best-women-dress', 'catalog.label' => 'Dresses'
			], [
				'catalog.code' => 'demo-best-women-top', 'catalog.label' => 'Tops'
			]]
		], [
			'catalog.code' => 'demo-best-men', 'catalog.label' => 'Men',
			'catalog' => [[
				'catalog.code' => 'demo-best-men-tshirt', 'catalog.label' => 'T-Shirts'
			], [
				'catalog.code' => 'demo-best-men-muscle', 'catalog.label' => 'Muscle shirts'
			]]
		], [
			'catalog.code' => 'demo-best-misc', 'catalog.label' => 'Misc',
			'catalog' => [[
				'catalog.code' => 'demo-best-misc-event', 'catalog.label' => 'Events'
			], [
				'catalog.code' => 'demo-best-misc-voucher', 'catalog.label' => 'Vouchers'
			]]
		]]
	], [
		'catalog.code' => 'demo-new', 'catalog.label' => 'New arrivals',
		'text' => [
			[
				'label' => 'New arrivals kurz',
				'content' => '<p><strong>Sommer 2021-2023</strong></p><p>Neue Collection eingetroffen</p>',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'New arrivals short',
				'content' => '<p><strong>Summer 2021-2023</strong></p><p>New collection available</p>',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Demo meta-description',
				'content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'type' => 'meta-description', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
		'media' => [
			[
				'label' => 'Demo: New arrivals stage', 'mimetype' => 'image/webp',
				'url' => 'https://aimeos.org/media/default/main-banner-2-big.webp',
				'preview' => [
					480 => 'https://aimeos.org/media/default/main-banner-2-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-2-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-2-big.webp',
				],
				'type' => 'stage', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
	], [
		'catalog.code' => 'demo-deals', 'catalog.label' => 'Hot deals',
		'text' => [
			[
				'label' => 'Hot deals kurz',
				'content' => '<p>Bis zu <strong>30%</strong> Rabatt<br />auf ausgewählte Stücke</p>',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Hot deals short',
				'content' => '<p>Up to <strong>30%</strong> discount<br />on selected items</p>',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
			[
				'label' => 'Demo meta-description',
				'content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'type' => 'meta-description', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
		'media' => [
			[
				'label' => 'Demo: Hot deals stage', 'mimetype' => 'image/webp',
				'url' => 'https://aimeos.org/media/default/main-banner-3-big.webp',
				'preview' => [
					480 => 'https://aimeos.org/media/default/main-banner-3-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-3-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-3-big.webp',
				],
				'type' => 'stage', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			],
		],
	]],
];
