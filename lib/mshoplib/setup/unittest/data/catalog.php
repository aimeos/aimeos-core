<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'catalog/lists/type' => [
		'attribute/default' => ['catalog.lists.type.domain' => 'attribute', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'catalog/default' => ['catalog.lists.type.domain' => 'catalog', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'media/default' => ['catalog.lists.type.domain' => 'media', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'price/default' => ['catalog.lists.type.domain' => 'price', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'product/default' => ['catalog.lists.type.domain' => 'product', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'service/default' => ['catalog.lists.type.domain' => 'service', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'text/default' => ['catalog.lists.type.domain' => 'text', 'catalog.lists.type.code' => 'default', 'catalog.lists.type.label' => 'Standard'],
		'product/promotion' => ['catalog.lists.type.domain' => 'product', 'catalog.lists.type.code' => 'promotion', 'catalog.lists.type.label' => 'Promotion'],

		'text/unittype1' => ['catalog.lists.type.domain' => 'text', 'catalog.lists.type.code' => 'unittype1', 'catalog.lists.type.label' => 'Unit type 1'],

		'product/new' => ['catalog.lists.type.domain' => 'product', 'catalog.lists.type.code' => 'new', 'catalog.lists.type.label' => 'New products'],
		'product/internet' => ['catalog.lists.type.domain' => 'product', 'catalog.lists.type.code' => 'internet', 'catalog.lists.type.label' => 'Online only'],
	],

	'catalog' => [[
		'catalog.code' => 'root', 'catalog.label' => 'Root', 'catalog.url' => 'home', 'catalog.config' => ['css-class' => 'home'],
		'catalog' => [[
			'catalog.code' => 'categories', 'catalog.label' => 'Categories', 'catalog.config' => ['css-class' => 'categories'],
			'catalog' => [[
				'catalog.code' => 'cafe', 'catalog.label' => 'Kaffee', 'catalog.url' => 'kaffee', 'catalog.config' => ['css-class' => 'coffee'],
				'lists' => [
					'product' => [[
						'catalog.lists.type' => 'promotion', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Expresso',
					], [
						'catalog.lists.type' => 'promotion', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Cappuccino',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Expresso',
					]],
					'text' => [[
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2010-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'catalog',
						'text.label' => 'cafe', 'text.content' => 'Kaffee',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2010-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'catalog',
						'text.label' => 'cafe_short_desc', 'text.content' => 'Eine kurze Beschreibung der Kaffeekategorie',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2010-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'catalog',
						'text.label' => 'cafe_long_desc', 'text.content' => 'Eine ausführliche Beschreibung der Kategorie. Hier machen auch angehängte Bilder zum Text einen Sinn.',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2010-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'deliveryinformation', 'text.domain' => 'catalog',
						'text.label' => 'cafe_delivery_desc', 'text.content' => 'Artikel dieser Kategorie können leider nicht in alle Länder verkauft werden, da sie den Einfuhrbedingungen nicht entsprechen. Um einige Kaffeebohnen ist noch die Katze herum! :D',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2010-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'paymentinformation', 'text.domain' => 'catalog',
						'text.label' => 'cafe_payment_desc', 'text.content' => 'Artikel dieser Kategorie können nur per Vorkasse bestellt werden.',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 5,
						'catalog.lists.datestart' => '2008-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'quote', 'text.domain' => 'catalog',
						'text.label' => 'cafe_quote', 'text.content' => 'Kaffee Bewertungen',
					]],
					'media' => [[
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'media.languageid' => 'de', 'media.type' => 'default', 'media.domain' => 'catalog',
						'media.label' => 'prod_123x103/195_prod_123x103.jpg', 'media.url' => 'prod_123x103/195_prod_123x103.jpg',
						'media.previews' => [1 => 'prod_123x103/195_prod_123x103.jpg'], 'media.mimetype' => 'image/jpeg',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2098-01-01 00:00:00',
						'media.languageid' => 'de', 'media.type' => 'stage', 'media.domain' => 'catalog',
						'media.label' => 'path/to/folder/cafe/stage.jpg', 'media.url' => 'path/to/folder/cafe/stage.jpg',
						'media.previews' => [1 => 'path/to/folder/cafe/stage.jpg'], 'media.mimetype' => 'image/jpeg',
					]],
				],
			], [
				'catalog.code' => 'tea', 'catalog.label' => 'Tee', 'catalog.url' => 'tee',
				'lists' => [
					'text' => [[
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'catalog',
						'text.label' => 'tea', 'text.content' => 'Tee',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'catalog',
						'text.label' => 'tea_short_desc', 'text.content' => 'Kurze Beschreibung der Teekategorie',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'catalog',
						'text.label' => 'tea_long_desc', 'text.content' => 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'deliveryinformation', 'text.domain' => 'catalog',
						'text.label' => 'tea_delivery_desc', 'text.content' => 'Tee wird in alle Länder geliefert. Allerdigs unterscheiden sich die Distributoren. Je nach Lagerung kann er sich in seiner Qualität unterscheiden.',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'paymentinformation', 'text.domain' => 'catalog',
						'text.label' => 'tea_payment_desc', 'text.content' => 'Es sind alle Zahlungsarten erlaubt.',
					]],
				],
			], [
				'catalog.code' => 'misc', 'catalog.label' => 'Misc',
				'lists' => [
					'product' => [[
						'catalog.lists.type' => 'promotion', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'ref' => 'Unittest: Test Selection',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'ref' => 'Unittest: Test priced Selection',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 5,
						'ref' => 'Unittest: Empty Selection',
					]],
					'text' => [[
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'catalog',
						'text.label' => 'misc', 'text.content' => 'Sonstiges',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'catalog',
						'text.label' => 'misc_short_desc', 'text.content' => 'Kurze Beschreibung der Kategorie',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'catalog',
						'text.label' => 'misc_long_desc', 'text.content' => 'Lange Beschreibung mit Bildern/Mediendaten.',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'deliveryinformation', 'text.domain' => 'catalog',
						'text.label' => 'misc_delivery_desc', 'text.content' => 'Versand nur innerhalb Europas.',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'paymentinformation', 'text.domain' => 'catalog',
						'text.label' => 'misc_payment_desc', 'text.content' => 'Zahlung nur per Kreditkarte möglich.',
					]],
					'media' => [[
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2098-01-01 00:00:00',
						'media.languageid' => 'de', 'media.type' => 'default', 'media.domain' => 'catalog',
						'media.label' => 'path/to/folder/example1.jpg', 'media.url' => 'path/to/folder/example1.jpg',
						'media.previews' => [1 => 'path/to/folder/example1.jpg'], 'media.mimetype' => 'image/jpeg',
						'property' => [[
							'media.property.type' => 'size', 'media.property.languageid' => null, 'media.property.value' => '1024',
						], [
							'media.property.type' => 'mtime', 'media.property.languageid' => null, 'media.property.value' => '2000-01-01 00:00:00',
						]]
					]],
				],
			]],
		], [
			'catalog.label' => 'Groups', 'catalog.code' => 'group',
			'lists' => [
				'media' => [[
					'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 3,
					'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2098-01-01 00:00:00',
					'media.languageid' => 'de', 'media.type' => 'icon', 'media.domain' => 'catalog',
					'media.label' => 'path/to/folder/example4.jpg', 'media.url' => 'path/to/folder/example4.jpg',
					'media.previews' => [1 => 'path/to/folder/example4.jpg'], 'media.mimetype' => 'image/jpeg',
				]],
			],
			'catalog' => [[
				'catalog.code' => 'new', 'catalog.label' => 'Neu',
				'lists' => [
					'product' => [[
						'catalog.lists.type' => 'new', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Expresso',
					], [
						'catalog.lists.type' => 'new', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Cappuccino',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Expresso',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Cappuccino',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Unittest: Bundle',
					]],
					'text' => [[
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'catalog',
						'text.label' => 'new', 'text.content' => 'Neu',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'catalog',
						'text.label' => 'new_long_desc', 'text.content' => 'Neue Produkte',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'url', 'text.domain' => 'catalog',
						'text.label' => 'new_metatitle', 'text.content' => 'Neu_im_Shop',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'meta-keyword', 'text.domain' => 'catalog',
						'text.label' => 'new_metakey', 'text.content' => 'neu',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'meta-description', 'text.domain' => 'catalog',
						'text.label' => 'new_metadesc', 'text.content' => 'Neue Produkte im Shop',
					]],
					'media' => [[
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2098-01-01 00:00:00',
						'media.languageid' => 'de', 'media.type' => 'prod_123x103', 'media.domain' => 'catalog',
						'media.label' => 'path/to/folder/example2.jpg', 'media.url' => 'path/to/folder/example2.jpg',
						'media.previews' => [1 => 'path/to/folder/example2.jpg'], 'media.status' => 0,
						'media.mimetype' => 'image/jpeg',
						'property' => [[
							'media.property.type' => 'copyright', 'media.property.languageid' => 'de', 'media.property.value' => 'ich, 2017',
						]]
					]],
				],
			], [
				'catalog.code' => 'internet', 'catalog.label' => 'Internet',
				'lists' => [
					'product' => [[
						'catalog.lists.type' => 'internet', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Expresso',
					], [
						'catalog.lists.type' => 'internet', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Cappuccino',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'Cafe Noire Cappuccino',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'ABCD/16 discs',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'MNOP/16 discs',
					], [
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'product', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'ref' => 'QRST/16 discs',
					]],
					'text' => [[
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 0,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'catalog',
						'text.label' => 'online', 'text.content' => 'Nur online',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 1,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'catalog',
						'text.label' => 'online_long_desc', 'text.content' => 'Ausschliesslich online erhältlich',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'url', 'text.domain' => 'catalog',
						'text.label' => 'online_metatitle', 'text.content' => 'Nur_im_Internet',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 3,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'meta-keyword', 'text.domain' => 'catalog',
						'text.label' => 'online_metakey', 'text.content' => 'internet',
					], [
						'catalog.lists.type' => 'unittype1', 'catalog.lists.domain' => 'text', 'catalog.lists.position' => 4,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2099-01-01 00:00:00',
						'text.languageid' => 'de', 'text.type' => 'meta-description', 'text.domain' => 'catalog',
						'text.label' => 'online_metadesc', 'text.content' => 'Nur online erhältlich',
					]],
					'media' => [[
						'catalog.lists.type' => 'default', 'catalog.lists.domain' => 'media', 'catalog.lists.position' => 2,
						'catalog.lists.datestart' => '2010-01-01 00:00:00', 'catalog.lists.dateend' => '2098-01-01 00:00:00',
						'media.languageid' => 'de', 'media.type' => 'prod_123x103', 'media.domain' => 'catalog',
						'media.label' => 'path/to/folder/example3.jpg', 'media.url' => 'path/to/folder/example3.jpg',
						'media.previews' => [1 => 'path/to/folder/example3.jpg'], 'media.status' => 0,
						'media.mimetype' => 'image/jpeg',
					]],
				],
			]],
		]],
	]],
];
