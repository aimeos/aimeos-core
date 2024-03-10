<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org], 2015-2024
 */

return [
	'catalog.code' => 'home', 'catalog.label' => 'Home', 'catalog.config' => ['css-class' => 'megamenu'],
	'text' => [
		[
			'text.label' => 'Demo name/de', 'text.content' => 'Start',
			'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo url/de', 'text.content' => 'Start',
			'text.type' => 'media.url', 'text.languageid' => 'de', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo short/de',
			'text.content' => 'Hohe Qualität, niedrige Preise!',
			'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 1, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo short/en',
			'text.content' => 'High quality, low prices!',
			'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 2, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo long/de',
			'text.content' => 'Hier finden Sie eine fantastische Auswahl zu günstigesten Preisen!',
			'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 3, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo long/en',
			'text.content' => 'Find a fantastic selection of products for the cheapest price!',
			'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 4, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
		[
			'text.label' => 'Demo meta-description',
			'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
			'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
			'catalog.list.type' => 'default', 'catalog.list.position' => 5, 'catalog.list.config' => [],
			'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
		],
	],
	'catalog' => [[
		'catalog.code' => 'demo-best', 'catalog.label' => 'Best sellers',
		'text' => [
			[
				'text.label' => 'Best seller kurz',
				'text.content' => '<p>Große Auswahl an TOP Sellern<br /><strong>BESTE Preise garantiert</strong></p>',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 1, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'Best seller short',
				'text.content' => '<p>LARGE selection of TOP sellers<br /><strong>BEST prices guaranteed</strong></p>',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 2, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Best seller stage', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/main-banner-1-big.webp',
				'media.previews' => [
					480 => 'https://aimeos.org/media/default/main-banner-1-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-1-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-1-big.webp',
				],
				'media.type' => 'stage', 'media.languageid' => null, 'media.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'media.label' => 'Demo: Best seller menu', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_01_A-low.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_01_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_01_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_01_A-big.webp',
				],
				'media.type' => 'menu', 'media.languageid' => null, 'media.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
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
				'text.label' => 'New arrivals kurz',
				'text.content' => '<p><strong>Sommer 2024</strong></p><p>Neue Collection eingetroffen</p>',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 1, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'New arrivals short',
				'text.content' => '<p><strong>Summer 2024</strong></p><p>New collection available</p>',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 2, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: New arrivals stage', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/main-banner-2-big.webp',
				'media.previews' => [
					480 => 'https://aimeos.org/media/default/main-banner-2-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-2-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-2-big.webp',
				],
				'media.type' => 'stage', 'media.languageid' => null, 'media.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
		],
	], [
		'catalog.code' => 'demo-deals', 'catalog.label' => 'Hot deals',
		'text' => [
			[
				'text.label' => 'Hot deals kurz',
				'text.content' => '<p>Bis zu <strong>30%</strong> Rabatt<br />auf ausgewählte Stücke</p>',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 1, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'Hot deals short',
				'text.content' => '<p>Up to <strong>30%</strong> discount<br />on selected items</p>',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 2, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Hot deals stage', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/main-banner-3-big.webp',
				'media.previews' => [
					480 => 'https://aimeos.org/media/default/main-banner-3-low.webp',
					960 => 'https://aimeos.org/media/default/main-banner-3-med.webp',
					1920 => 'https://aimeos.org/media/default/main-banner-3-big.webp',
				],
				'media.type' => 'stage', 'media.languageid' => null, 'media.status' => 1,
				'catalog.list.type' => 'default', 'catalog.list.position' => 0, 'catalog.list.config' => [],
				'catalog.list.start' => null, 'catalog.list.end' => null, 'catalog.list.status' => 1
			],
		],
	]],
];
