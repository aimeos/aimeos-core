<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org], 2015-2021
 */

return [

	// Single article
	[
		'product.code' => 'demo-article', 'product.type' => 'default',
		'product.label' => 'Demo article', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article',
				'text.content' => 'Demo article',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_01_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_01_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_01_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_01_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_01_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_01_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_01_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_01_B-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
		],
		'attribute' => [
			[
				'attribute.code' => 'demo-black', 'attribute.label' => 'Demo: Black',
				'attribute.type' => 'color', 'attribute.position' => 1, 'attribute.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
				'text' => [
					[
						'text.label' => 'Demo name/de: Schwarz',
						'text.content' => 'Schwarz',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Black',
						'text.content' => 'Black',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/de: Schwarz',
						'text.content' => 'Schwarz',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Black',
						'text.content' => 'Black',
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
						'text.content' => 'Kleiner-Aufdruck',
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
						'text.content' => 'Grosser-Aufdruck',
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
						'text.content' => 'print text',
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

	// Selection articles
	[
		'product.code' => 'demo-selection-article-1', 'product.type' => 'default',
		'product.label' => 'Demo variant article 1', 'product.status' => 1,
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
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Beige',
				'attribute.type' => 'color', 'attribute.position' => 0, 'attribute.status' => 1,
				'product.lists.type' => 'variant', 'product.lists.position' => 0,
				'text' => [
					[
						'text.label' => 'Demo name/de: Beige', 'text.content' => 'Beige',
						'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo name/en: Beige', 'text.content' => 'Beige',
						'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 1
					],
					[
						'text.label' => 'Demo url/de: Beige', 'text.content' => 'Beige',
						'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
						'attribute.lists.type' => 'default', 'attribute.lists.position' => 0
					],
					[
						'text.label' => 'Demo url/en: Beige', 'text.content' => 'beige',
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
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Beige',
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
				'attribute.code' => 'demo-beige', 'attribute.label' => 'Demo: Beige',
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
		'product.label' => 'Demo selection article', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel mit Auswahl',
				'text.content' => 'Demoartikel mit Auswahl',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de: Demoartikel mit Auswahl',
				'text.content' => 'Demoartikel-mit-Auswahl',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels mit Auswahl',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels mit Auswahl, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo selection article',
				'text.content' => 'Demo selection article',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the selection demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the selection demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Selection article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_04_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_04_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_04_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_04_A-big.jpg',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
			[
				'media.label' => 'Demo: Selection article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_04_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_04_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_04_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_04_B-big.jpg',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1,
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

	// bundle article
	[
		'product.code' => 'demo-bundle-article', 'product.type' => 'bundle',
		'product.label' => 'Demo bundle article', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel mit Bundle',
				'text.content' => 'Demoartikel mit Bundle',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo url/de: Demoartikel mit Bundle',
				'text.content' => 'Demoartikel-mit-Bundle',
				'text.type' => 'url', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels mit Bundle',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels mit Bundle, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo bundle article',
				'text.content' => 'Demo bundle article',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the bundle demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the bundle demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Bundle article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_03_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_03_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_03_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_03_A-big.jpg',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
			[
				'media.label' => 'Demo: Bundle article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_03_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_03_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_03_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_03_B-big.jpg',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1,
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
				'product.code' => 'demo-selection-article',
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'product.code' => 'demo-article',
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
		],
		'stock' => [
			['stock.stocklevel' => 10, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
	[
		'product.code' => 'demo-voucher', 'product.type' => 'voucher',
		'product.label' => 'Demo voucher', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Gutschein',
				'text.content' => 'Demo-Gutschein',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demo-Gutscheins',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Gutscheins, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo voucher',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo voucher.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo voucher that may be a little bit longer.',
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
				'media.label' => 'Demo: Voucher 0.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/voucher-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/voucher-low.jpg',
					720 => 'https://aimeos.org/media/default/voucher-med.jpg',
					1350 => 'https://aimeos.org/media/default/voucher-big.jpg',
				],
				'media.type' => 'default', 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0,
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],
	[
		'product.code' => 'demo-rebate', 'product.type' => 'default',
		'product.label' => 'Demo rebate', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Rabatt',
				'text.content' => 'Demorabatt',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
		],
		'stock' => [
			['stock.stocklevel' => null, 'stock.type' => 'default', 'stock.dateback' => null],
		],
	],

	// Single articles
	[
		'product.code' => 'demo-article-2', 'product.type' => 'default',
		'product.label' => 'Demo article 2', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 2',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 2',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_02_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_02_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_02_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_02_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_02_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_02_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_02_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_02_B-big.jpg',
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
		'product.label' => 'Demo article 3', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 3',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 3',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_05_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_05_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_05_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_05_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_05_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_05_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_05_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_05_B-big.jpg',
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
		'product.label' => 'Demo article 4', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 4',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 4',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_06_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_06_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_06_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_06_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_06_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_06_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_06_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_06_B-big.jpg',
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
		'product.label' => 'Demo article 5', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 5',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 5',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_07_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_07_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_07_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_07_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_07_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_07_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_07_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_07_B-big.jpg',
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
		'product.label' => 'Demo article 6', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 6',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 6',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_08_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_08_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_08_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_08_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_08_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_08_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_08_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_08_B-big.jpg',
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
		'product.code' => 'demo-article-7', 'product.type' => 'default',
		'product.label' => 'Demo article 7', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 7',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 7',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_09_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_09_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_09_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_09_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_09_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_09_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_09_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_09_B-big.jpg',
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
		'product.code' => 'demo-article-8', 'product.type' => 'default',
		'product.label' => 'Demo article 8', 'product.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Demoartikel', 'text.content' => 'Demoartikel 8',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en: Demo article', 'text.content' => 'Demo article 8',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
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
				'price.value' => '39.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0
			],
			[
				'price.label' => 'Demo: Article from 1',
				'price.value' => '49.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 3
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_10_A-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_10_A-low.jpg',
					720 => 'https://aimeos.org/media/default/product_10_A-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_10_A-big.jpg',
				],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'product.lists.type' => 'default', 'product.lists.position' => 0, 'product.lists.config' => [],
				'product.lists.start' => null, 'product.lists.end' => null, 'product.lists.status' => 1,
			],
			[
				'media.label' => 'Demo: Article 2.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/product_10_B-big.jpg',
				'media.previews' => [
					240 => 'https://aimeos.org/media/default/product_10_B-low.jpg',
					720 => 'https://aimeos.org/media/default/product_10_B-med.jpg',
					1350 => 'https://aimeos.org/media/default/product_10_B-big.jpg',
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
];
