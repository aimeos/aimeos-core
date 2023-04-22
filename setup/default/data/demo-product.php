<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org], 2015-2023
 */

return [

	// Single article
	[
		'product.code' => 'demo-article', 'product.type' => 'default',
		'product.label' => 'Dark grey dress', 'product.status' => 1,
		'rating' => '4.8', 'ratings' => 20,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'promotion', 'product.lists.position' => 0
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 1
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 5
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'supplier' => [[
			'supplier.code' => 'demo-hr', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Dunkelgraues Kleid',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Elastisches Kleid in dunkelgrau',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Das elastische Kleid in der Modefarbe dunkelgrau unterstreicht Ihre Figur',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en',
				'text.content' => 'Dark grey dress',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Elastic dress in dark grey',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'The elastic dress in fashion color dark gray emphasizes your figure',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '100.00', 'price.costs' => '5.00', 'price.rebate' => '20.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '130.00', 'price.costs' => '7.50', 'price.rebate' => '30.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_01_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_01_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_01_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_01_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_01_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_01_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_01_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_01_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'attribute' => [
			[
				'attribute.code' => 'demo-black', 'attribute.label' => 'Demo: Dark',
				'attribute.type' => 'color', 'attribute.position' => 1, 'attribute.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
				'text' => [
					[
						'text.label' => 'Demo name/de',
						'text.content' => 'Dunkel',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en',
						'text.content' => 'Dark',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de',
						'text.content' => 'dunkel',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en',
						'text.content' => 'dark',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
				'media' => [
					[
						'media.label' => 'Demo: black.gif', 'media.mimetype' => 'image/gif',
						'media.url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=',
						'media.previews' => [1 => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs='],
						'media.type' => 'icon', 'media.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-print-small', 'attribute.label' => 'Demo: Small print',
				'attribute.type' => 'print', 'attribute.position' => 0, 'status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name/de: Kleiner Aufdruck',
						'text.content' => 'Kleiner Aufdruck',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Small print',
						'text.content' => 'Small print',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de: Kleiner Aufdruck',
						'text.content' => 'kleiner-aufdruck',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Small print',
						'text.content' => 'small-print',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
				'price' => [
					[
						'price.label' => 'Demo: Small print',
						'price.value' => '5.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'price.label' => 'Demo: Small print',
						'price.value' => '7.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
			],
			[
				'attribute.code' => 'demo-print-large', 'attribute.label' => 'Demo: Large print',
				'attribute.type' => 'print', 'attribute.position' => 1, 'attribute.status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 2,
				'text' => [
					[
						'text.label' => 'Demo name/de: Grosser Aufdruck',
						'text.content' => 'Grosser Aufdruck',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Large print',
						'text.content' => 'Large print',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de: Grosser Aufdruck',
						'text.content' => 'grosser-aufdruck',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Large print',
						'text.content' => 'large-print',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
				'price' => [
					[
						'price.label' => 'Demo: Large print',
						'price.value' => '15.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'price.label' => 'Demo: Large print',
						'price.value' => '20.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
			],
			[
				'attribute.code' => 'demo-print-text', 'attribute.label' => 'Demo: Text for print',
				'attribute.type' => 'text', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'custom', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name/de: Kleiner Aufdruck',
						'text.content' => 'Text Aufdruck',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Small print',
						'text.content' => 'Text print',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'P0Y1M0W0D', 'attribute.label' => 'Demo: One month',
				'attribute.type' => 'interval', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name/de: Ein Monat',
						'text.content' => '1 Monat',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: One Month',
						'text.content' => '1 month',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'P1Y0M0W0D', 'attribute.label' => 'Demo: One year',
				'attribute.type' => 'interval', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name/de: Ein Jahr',
						'text.content' => '1 Jahr',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: One year',
						'text.content' => '1 year',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
		],
		'property' => [
			['product.property.type' => 'package-length', 'product.property.value' => '20.00'],
			['product.property.type' => 'package-width', 'product.property.value' => '10.00'],
			['product.property.type' => 'package-height', 'product.property.value' => '5.00'],
			['product.property.type' => 'package-weight', 'product.property.value' => '2.5'],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-2', 'product.type' => 'default',
		'product.label' => 'Red T-Shirt', 'product.status' => 1,
		'rating' => '4.6', 'ratings' => 15,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'promotion', 'product.lists.position' => 1
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 6
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 4
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 2
		]],
		'supplier' => [[
			'supplier.code' => 'demo-hr', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Rotes T-Shirt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Basic Shirt für Männer in rot',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Dieses eng anliegende T-Shirt in rot lenkt die Aufmerksamkeit auf den Oberkörper und betont ihn',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Red T-Shirt',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Basic Shirt for men in red',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'This tight fitting t-shirt in red draws attention to the upper body and emphasizes it',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '49.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '59.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_02_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_02_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_02_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_02_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_02_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_02_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_02_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_02_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-3', 'product.type' => 'default',
		'product.label' => 'Black shirt', 'product.status' => 1,
		'rating' => '4.9', 'ratings' => 10,
		'catalog' => [[
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 7
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 2
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 5
		]],
		'supplier' => [[
			'supplier.code' => 'demo-ballroom', 'product.lists.type' => 'default', 'product.lists.position' => 1
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Schwarzes Shirt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de',
				'text.content' => 'black-shirt-frauen',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Schwarzes Basic-Shirt für Frauen',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Dieses schwarze Basic-Shirt für Frauen ist ein zeitloses Kleidungsstück, das in jedem Kleiderschrank zu finden sein sollte',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Black shirt',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo url/en',
				'text.content' => 'black-shirt-women',
				'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Black basic shirt for women',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'This basic black shirt for women is a timeless garment that should be in every women\'s closet',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '69.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '79.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_05_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_05_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_05_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_05_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_05_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_05_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_05_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_05_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-4', 'product.type' => 'default',
		'product.label' => 'Black T-Shirt', 'product.status' => 1,
		'rating' => '4.5', 'ratings' => 4,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 4
		]],
		'supplier' => [[
			'supplier.code' => 'demo-ballroom', 'product.lists.type' => 'default', 'product.lists.position' => 1
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Schwarzes T-Shirt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Basic T-Shirt für Männer in schwarz',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Dieses schwarze Basic-T-Shirt für Männer ist ein unverzichtbares Kleidungsstück, das in jeder Garderobe zu finden sein sollte',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Demo article 4',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'This basic black t-shirt for men is an essential garment that should be in every wardrobe',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '29.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '36.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_06_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_06_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_06_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_06_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_06_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_06_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_06_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_06_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-5', 'product.type' => 'default',
		'product.label' => 'Short-sleeved shirt', 'product.status' => 1,
		'rating' => '4.75', 'ratings' => 8,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 5
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 8
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 1
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 4
		]],
		'supplier' => [[
			'supplier.code' => 'demo-cstory', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Kurzarm-Shirt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Trendiges Kurzarm-Shirt in schwarz/weiß',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Das trendige Kurzarm-Shirt in Schwarz-Weiß ist ein absoluter Hingucker und ein Muss für jeden, der gerne modisch und stylisch gekleidet sein möchte',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Short-sleeved shirt',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Trendy short-sleeved shirt in black/white',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'The trendy short-sleeved shirt in black and white is an absolute eye-catcher and a must for everyone who likes to be fashionable and stylishly dressed',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '79.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '99.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_07_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_07_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_07_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_07_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_07_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_07_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_07_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_07_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-6', 'product.type' => 'default',
		'product.label' => 'Sexy top', 'product.status' => 1,
		'rating' => '4.2', 'ratings' => 25,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'promotion', 'product.lists.position' => 2,
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 6
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 0
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 7
		]],
		'supplier' => [[
			'supplier.code' => 'demo-hr', 'product.lists.type' => 'default', 'product.lists.position' => 1
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Sexy Top',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Tank-top in beige mit weitem Ausschnitt',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Das Tank-Top in Beige mit weitem Ausschnitt ist ein perfektes Kleidungsstück für warme Tage, da es nicht nur luftig und bequem ist, sondern auch einen stilvollen Look bietet',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Sexy top',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Tank top in beige with wide neckline',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Beige tank top with wide neckline is a perfect garment for warm days, as it is not only airy and comfortable, but also offers a stylish look',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '19.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '22.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Bundle article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_03_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_03_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_03_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_03_A-big.webp',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
			[
				'media.label' => 'Demo: Bundle article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_03_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_03_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_03_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_03_B-big.webp',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	[
		'product.code' => 'demo-article-7', 'product.type' => 'default',
		'product.label' => 'Tank-Top in black', 'product.status' => 1,
		'rating' => '4.5', 'ratings' => 12,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 7
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 9
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 3
		]],
		'supplier' => [[
			'supplier.code' => 'demo-cstory', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Tank-Top in schwarz',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Stylishes Tank-Top für Männer',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Mit seinem ärmellosen Design und dem bequemen Schnitt bietet es nicht nur eine hohe Bewegungsfreiheit, sondern auch ein modernes und stylisches Aussehen',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Tank-Top in black',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Stylish tank top for men',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'With its sleeveless design and comfortable cut, it offers not only a high freedom of movement, but also a modern and stylish look',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '49.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '59.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_09_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_09_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_09_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_09_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_09_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_09_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_09_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_09_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// Voucher product
	[
		'product.code' => 'demo-voucher', 'product.type' => 'voucher',
		'product.label' => 'Gift voucher', 'product.status' => 1,
		'rating' => '5.0', 'ratings' => 7,
		'catalog' => [[
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 9
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Gutschein',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Geschenk-Gutschein für Freunde',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Schenken Sie Ihren Freunden Freude mit einem Gutschein',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Gift certificate',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'A gift for your friends',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Give joy to your friends with a gift certificate',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Voucher',
				'price.value' => '25.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Voucher',
				'price.value' => '25.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
		],
		'attribute' => [
			[
				'attribute.code' => 'custom', 'attribute.label' => 'Custom price',
				'attribute.type' => 'price', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'custom', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'name/de: Gutscheinwert',
						'text.content' => 'Gutscheinwert',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'name/en: Voucher value',
						'text.content' => 'Voucher value',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-custom-date', 'attribute.label' => 'Demo: Custom date',
				'attribute.type' => 'date', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'custom', 'product.lists.position' => 2,
				'text' => [
					[
						'text.label' => 'Demo name/de: Kundendatum',
						'text.content' => 'Kundendatum',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Customer date',
						'text.content' => 'Customer date',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Voucher 0.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/voucher-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/voucher-low.webp',
					720 => 'https://aimeos.org/media/default/voucher-med.webp',
					1350 => 'https://aimeos.org/media/default/voucher-big.webp',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// bundle article
	[
		'product.code' => 'demo-bundle-article', 'product.type' => 'bundle',
		'product.label' => 'Shirt & cap', 'product.status' => 1,
		'rating' => '4.3', 'ratings' => 10,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 2
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 3
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 7
		]],
		'supplier' => [[
			'supplier.code' => 'demo-sb', 'product.lists.type' => 'default', 'product.lists.position' => 0
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Shirt & Mütze',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de',
				'text.content' => 'shirt-muetze',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Coole Kombination aus T-Shirt und Mütze',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Lässige Kombination aus cooler Mütze mit trendigem T-Shirt in dunkelgrau',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en',
				'text.content' => 'Shirt & cap',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Cool combination of shirt and cap',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Casual combination of cool cap with trendy t-shirt in dark gray',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Bundle article from 1',
				'price.value' => '250.00', 'price.costs' => '10.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Bundle article from 5',
				'price.value' => '235.00', 'price.costs' => '10.00', 'price.rebate' => '15.00', 'price.taxrate' => '10.00',
				'price.quantity' => 5, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'price.label' => 'Demo: Bundle article from 10',
				'price.value' => '220.00', 'price.costs' => '10.00', 'price.rebate' => '30.00', 'price.taxrate' => '10.00',
				'price.quantity' => 10, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'price.label' => 'Demo: Bundle article from 1',
				'price.value' => '250.00', 'price.costs' => '15.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'price.label' => 'Demo: Bundle article from 5',
				'price.value' => '225.00', 'price.costs' => '15.00', 'price.rebate' => '25.00', 'price.taxrate' => '5.00',
				'price.quantity' => 5, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'price.label' => 'Demo: Bundle article from 10',
				'price.value' => '200.00', 'price.costs' => '15.00', 'price.rebate' => '50.00', 'price.taxrate' => '5.00',
				'price.quantity' => 10, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_08_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_08_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_08_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_08_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_08_B-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_08_B-low.webp',
					720 => 'https://aimeos.org/media/default/product_08_B-med.webp',
					1350 => 'https://aimeos.org/media/default/product_08_B-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'attribute' => [
			[
				'attribute.code' => 'demo-sticker-small', 'attribute.label' => 'Demo: Small sticker',
				'attribute.type' => 'sticker', 'attribute.position' => 2, 'attribute.status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name/de: Kleines Etikett',
						'text.content' => 'Kleines Etikett',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Small sticker',
						'text.content' => 'Small sticker',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de: Kleines Etikett',
						'text.content' => 'Kleines-Etikett',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Small sticker',
						'text.content' => 'small-sticker',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
				'price' => [
					[
						'price.label' => 'Demo: Small sticker',
						'price.value' => '2.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'price.label' => 'Demo: Small sticker',
						'price.value' => '3.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
			],
			[
				'attribute.code' => 'demo-sticker-large', 'attribute.label' => 'Demo: Large sticker',
				'attribute.type' => 'sticker', 'attribute.position' => 3, 'attribute.status' => 1,
				'product.lists.type' => 'config', 'product.lists.position' => 2,
				'text' => [
					[
						'text.label' => 'Demo name/de: Grosses Etikett',
						'text.content' => 'Großes Etikett',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Large sticker',
						'text.content' => 'Large sticker',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de: Grosses Etikett',
						'text.content' => 'Grosses-Etikett',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Large sticker',
						'text.content' => 'large-sticker',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
				'price' => [
					[
						'price.label' => 'Demo: Large sticker',
						'price.value' => '5.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'price.label' => 'Demo: Large sticker',
						'price.value' => '7.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
						'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
			],
		],
		'product' => [
			[
				'product.code' => 'demo-article-3',
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'product.code' => 'demo-voucher',
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
		],
		'stock' => [
			['stock.stocklevel' => 10, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// group article
	[
		'product.code' => 'demo-group-article', 'product.type' => 'group',
		'product.label' => 'Shirts for women', 'product.status' => 1,
		'rating' => '4.0', 'ratings' => 13,
		'catalog' => [[
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 5
		], [
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 5
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 7
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Shirts für Frauen',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de',
				'text.content' => 'shirts-fuer-frauen',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Alle Shirts für Frauen',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Unsere Angebote an Shirts für Frauen im Überblick',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en',
				'text.content' => 'Shirts for women',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'All shirts for women',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Our offers of shirts for women at a glance',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Group article from 1',
				'price.value' => '49.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Group article from 1',
				'price.value' => '59.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_05_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_05_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_05_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_05_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_07_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_07_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_07_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_07_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 3.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_03_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_03_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_03_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_03_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 4.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_08_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_08_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_08_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_08_A-big.webp',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'product' => [
			[
				'product.code' => 'demo-article-3',
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'product.code' => 'demo-article-5',
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'product.code' => 'demo-article-6',
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'product.code' => 'demo-bundle-article',
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],


	// Selection articles
	[
		'product.code' => 'demo-selection-article-1', 'product.type' => 'default',
		'product.label' => 'Demo variant article 1', 'product.status' => 1,
		'supplier' => [[
			'supplier.code' => 'demo-cstory', 'product.lists.type' => 'default', 'product.lists.position' => 1
		]],
		'attribute' => [
			[
				'attribute.code' => 'demo-blue', 'attribute.label' => 'Demo: Blue',
				'attribute.type' => 'color', 'attribute.position' => 2, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 0,
				'text' => [
					[
						'text.label' => 'Demo name/de: Blau',
						'text.content' => 'Blau',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Blue',
						'text.content' => 'Blue',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
					[
						'text.label' => 'Demo url/de: Blau',
						'text.content' => 'Blau',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Blue',
						'text.content' => 'blue',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
				'media' => [
					[
						'media.label' => 'Demo: blue.gif', 'media.mimetype' => 'image/gif',
						'media.url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAA/wAAACwAAAAAAQABAAACAkQBADs=',
						'media.previews' => [1 => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAA/wAAACwAAAAAAQABAAACAkQBADs='],
						'media.type' => 'icon', 'media.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-width-32', 'attribute.label' => 'Demo: Width 32',
				'attribute.type' => 'width', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name: Width 32', 'text.content' => '32',
						'text.type' => 'name', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url: Width 32', 'text.content' => 'Weite-32',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url: Width 32', 'text.content' => 'width-32',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-length-34', 'attribute.label' => 'Demo: Length 34',
				'attribute.type' => 'length', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name: Length 34', 'text.content' => '34',
						'text.type' => 'name', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
					[
						'text.label' => 'Demo url: Length 34', 'text.content' => 'Länge-34',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
					[
						'text.label' => 'Demo url: Length 34', 'text.content' => 'length-34',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
				],
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Selection article 1 from 1',
				'price.value' => '140.00', 'price.costs' => '10.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Selection article 1 from 1',
				'price.value' => '190.00', 'price.costs' => '15.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'stock' => [
			['stock.stocklevel' => 5, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
	[
		'product.code' => 'demo-selection-article-2', 'product.type' => 'default',
		'product.label' => 'Demo variant article 2', 'product.status' => 1,
		'attribute' => [
			[
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Light',
				'attribute.type' => 'color', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 0,
				'text' => [
					[
						'text.label' => 'Demo name/de', 'text.content' => 'Hell',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Light', 'text.content' => 'Light',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
					[
						'text.label' => 'Demo url/de: Light', 'text.content' => 'Hell',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Light', 'text.content' => 'beige',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
				],
				'media' => [
					[
						'media.label' => 'Demo: beige.gif', 'media.mimetype' => 'image/gif',
						'media.url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAPX13AAAACwAAAAAAQABAAACAkQBADs=',
						'media.previews' => [1 => 'data:image/gif;base64,R0lGODdhAQABAIAAAPX13AAAACwAAAAAAQABAAACAkQBADs='],
						'media.type' => 'icon', 'media.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-width-33', 'attribute.label' => 'Demo: Width 33',
				'attribute.type' => 'width', 'attribute.position' => 1, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 1,
				'text' => [
					[
						'text.label' => 'Demo name: Width 33', 'text.content' => '33',
						'text.type' => 'name', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url: Width 33', 'text.content' => 'Weite-33',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url: Width 33', 'text.content' => 'width-33',
						'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
				],
			],
			[
				'attribute.code' => 'demo-length-36', 'attribute.label' => 'Demo: Length 36',
				'attribute.type' => 'length', 'attribute.position' => 1, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 2,
				'text' => [
					[
						'text.label' => 'Demo name: Length 36', 'text.content' => '36',
						'text.type' => 'name', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
					[
						'text.label' => 'Demo url: Length 36', 'text.content' => 'Länge-36',
						'text.type' => 'url', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
					[
						'text.label' => 'Demo url: Length 36', 'text.content' => 'length-36',
						'text.type' => 'url', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 2
					],
				],
			],
		],
		'stock' => [
			['stock.stocklevel' => 0, 'stock.type' => 'default', 'stock.dateback' => '2015-01-01 12:00:00'],
		],
	],
	[
		'product.code' => 'demo-selection-article-3', 'product.type' => 'default',
		'product.label' => 'Demo variant article 3', 'product.status' => 1,
		'attribute' => [
			[
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Light',
				'attribute.type' => 'color', 'attribute.position' => 2, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 0,
			],
			[
				'attribute.code' => 'demo-width-32', 'attribute.label' => 'Demo: Width 32',
				'attribute.type' => 'width', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 1,
			],
			[
				'attribute.code' => 'demo-length-34', 'attribute.label' => 'Demo: Length 36',
				'attribute.type' => 'length', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 2,
			],
		],
		'stock' => [
			['stock.stocklevel' => 10, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
	[
		'product.code' => 'demo-selection-article-4', 'product.type' => 'default',
		'product.label' => 'Demo variant article 4', 'product.status' => 1,
		'attribute' => [
			[
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Light',
				'attribute.type' => 'color', 'attribute.position' => 2, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 0,
			],
			[
				'attribute.code' => 'demo-width-33', 'attribute.label' => 'Demo: Width 33',
				'attribute.type' => 'width', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 1,
			],
			[
				'attribute.code' => 'demo-length-34', 'attribute.label' => 'Demo: Length 34',
				'attribute.type' => 'length', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 2,
			],
		],
		'stock' => [
			['stock.stocklevel' => 3, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
	[
		'product.code' => 'demo-selection-article', 'product.type' => 'select',
		'product.label' => 'Black shirt', 'product.status' => 1,
		'rating' => '4.4', 'ratings' => 25,
		'catalog' => [[
			'catalog.code' => 'home', 'product.lists.type' => 'default', 'product.lists.position' => 1
		], [
			'catalog.code' => 'demo-best', 'product.lists.type' => 'default', 'product.lists.position' => 2
		], [
			'catalog.code' => 'demo-new', 'product.lists.type' => 'default', 'product.lists.position' => 6
		]],
		'supplier' => [[
			'supplier.code' => 'demo-ballroom', 'product.lists.type' => 'default', 'product.lists.position' => 1
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Schwarzes T-Shirt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de',
				'text.content' => 'schwarzes-shirt-maenner',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Stylisches, schwarzes T-Shirt für Männer',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Dieses schwarzes T-Shirt für Männer ist ein must-have in jedem Kleiderschrank!',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en',
				'text.content' => 'Black shirt',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo url/en',
				'text.content' => 'black-shirt-men',
				'text.type' => 'url', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Stylish black shirt for men',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'This black t-shirt for men is a must-have in every men\'s closet!',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Selection article from 1',
				'price.value' => '150.00', 'price.costs' => '10.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Selection article from 5',
				'price.value' => '135.00', 'price.costs' => '10.00', 'price.rebate' => '15.00', 'price.taxrate' => '10.00',
				'price.quantity' => 5, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'price.label' => 'Demo: Selection article from 10',
				'price.value' => '120.00', 'price.costs' => '10.00', 'price.rebate' => '30.00', 'price.taxrate' => '10.00',
				'price.quantity' => 10, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'price.label' => 'Demo: Selection article from 1',
				'price.value' => '200.00', 'price.costs' => '15.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'price.label' => 'Demo: Selection article from 5',
				'price.value' => '175.00', 'price.costs' => '15.00', 'price.rebate' => '25.00', 'price.taxrate' => '5.00',
				'price.quantity' => 5, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'price.label' => 'Demo: Selection article from 10',
				'price.value' => '150.00', 'price.costs' => '15.00', 'price.rebate' => '50.00', 'price.taxrate' => '5.00',
				'price.quantity' => 10, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Selection article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/product_04_A-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_04_A-low.webp',
					720 => 'https://aimeos.org/media/default/product_04_A-med.webp',
					1350 => 'https://aimeos.org/media/default/product_04_A-big.webp',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
		],
		'product' => [
			[
				'product.code' => 'demo-selection-article-1',
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'product.code' => 'demo-selection-article-2',
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'product.code' => 'demo-article',
				'product.lists.type' => 'suggestion', 'product.lists.position' => 0
			],
			[
				'product.code' => 'demo-article',
				'product.lists.type' => 'bought-together', 'product.lists.position' => 0
			],
		],
		'stock' => [
			['stock.stocklevel' => 5, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// event article
	[
		'product.code' => 'demo-event-article', 'product.type' => 'event',
		'product.label' => 'Fashion week', 'product.status' => 1,
		'product.datestart' => '2100-01-01 08:00:00', 'product.dateend' => '2100-01-01 16:00:00',
		'rating' => '0', 'ratings' => 0,
		'catalog' => [[
			'catalog.code' => 'demo-deals', 'product.lists.type' => 'default', 'product.lists.position' => 10
		]],
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Fashion Week',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de',
				'text.content' => 'fashion-week',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Ticket zum Event des Jahres',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Erhalten Sie Eintritt zu diesjährigen Fashion Week in Paris, dem exklusiven Event der Modebranche!',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en',
				'text.content' => 'Fashion week',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Ticket for the event of the year',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Get entry to this year\'s Fashion Week in Paris, the exclusive event of the fashion industry!',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 6
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Event article from 1',
				'price.value' => '49.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Event article from 1',
				'price.value' => '59.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '5.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Bundle article 1.webp', 'media.mimetype' => 'image/webp',
				'media.url' => 'https://aimeos.org/media/default/event-big.webp',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/event-low.webp',
					720 => 'https://aimeos.org/media/default/event-med.webp',
					1350 => 'https://aimeos.org/media/default/event-big.webp',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
		],
		'stock' => [
			['stock.stocklevel' => 100, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// Rebate product
	[
		'product.code' => 'demo-rebate', 'product.type' => 'default',
		'product.label' => 'Discount', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de',
				'text.content' => 'Rabatt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
];
