<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2017-2023
 */

return [
	[
		'supplier.code' => 'demo-hr', 'supplier.label' => 'H&R', 'supplier.status' => 1,
		'address' => [
			[
				'supplier.address.salutation' => 'company', 'company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test street', 'supplier.address.address2' => '1',
				'supplier.address.address3' => '', 'supplier.address.postal' => '10000',
				'supplier.address.city' => 'Test city', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo1@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			],
		],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'H&R',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Kleidung zu bezahlbaren Preisen',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'H&R ist eine norwegische Bekleidungsfirma, die weltweit für
					ihre Modekollektionen zu erschwinglichen Preisen bekannt ist. Sie ist heute
					eine der größten Modeketten der Welt mit mehr als 5.000 Geschäften in über 70
					Ländern. Die Marke bietet Kleidung für Frauen, Männer, Jugendliche und Kinder an,
					sowie eine breite Palette an Accessoires und Kosmetikprodukten',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'H&R',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Fashion for affordable prices',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'H&R is a Norwegian clothing company that is known worldwide for
					its fashion collections at affordable prices. It is today one of the largest
					fashion chains in the world, with more than 5,000 stores in over 70 countries.
					countries. The brand offers clothing for women, men, teenagers and children,
					as well as a wide range of accessories and cosmetic products',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Supplier logo 1', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-1.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-1.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			],
		],
	],
	[
		'supplier.code' => 'demo-cstory', 'supplier.label' => 'C-Story', 'supplier.status' => 1,
		'address' => [
			[
				'supplier.address.salutation' => 'company', 'company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test street', 'supplier.address.address2' => '1',
				'supplier.address.address3' => '', 'supplier.address.postal' => '10000',
				'supplier.address.city' => 'Test city', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo1@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			],
		],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'C-Story',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Trendige Kleidung für Männer, Frauen und Kinder',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'C-Story ist bekannt für ihre trendorientierte Mode. Das
					Unternehmen produziert und vertreibt Kleidung, Schuhe und Accessoires für
					Männer, Frauen und Kinder. C-Story entwirft und produziert Kleidungsstücke
					in kleinen Chargen und aktualisiert ihre Kollektionen sehr regelmäßig,
					um die neuesten Trends aufzugreifen',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'C-Story',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Trendy fashion for men, women and kids',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'C-Story is known for their trendy fashion. The
					company produces and distributes clothing, shoes and accessories for
					men, women and children. C-Story designs and manufactures garments
					in small batches and updates its collections very regularly,
					to pick up the latest trends',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Supplier logo 2', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-1.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-1.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			],
		],
	],
	[
		'supplier.code' => 'demo-sb', 'supplier.label' => 'Sergio Blunic', 'supplier.status' => 1,
		'address' => [
			[
				'supplier.address.salutation' => 'company', 'company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test street', 'supplier.address.address2' => '1',
				'supplier.address.address3' => '', 'supplier.address.postal' => '10000',
				'supplier.address.city' => 'Test city', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo1@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			],
		],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Sergio Blunic',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Ikonische und elegante Schuhdesigns aus Portugal',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Sergio Blunic ist ein portugiesischer Modedesigner, der für
					seine ikonischen und eleganten Schuhkollektionen bekannt ist. Blunic erlangte
					internationale Bekanntheit für seine innovativen Schuhdesigns. Seine Schuhe
					zeichnen sich durch raffinierte Formen, hochwertige Materialien und
					Handwerkskunst aus',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Sergio Blunic',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Iconic and elegant shoe designs from Portugal',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Sergio Blunic is a Portuguese fashion designer who is known
					for his iconic and elegant shoe collections. Blunic gained international
					fame for his innovative shoe designs. His shoes are characterized by refined
					shapes, high quality materials and craftsmanship',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Supplier logo 3', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-3.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-3.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			],
		],
	],
	[
		'supplier.code' => 'demo-ballroom', 'supplier.label' => 'Ballroom', 'supplier.status' => 1,
		'address' => [
			[
				'supplier.address.salutation' => 'company', 'supplier.address.company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test road', 'supplier.address.address2' => '10',
				'supplier.address.address3' => '', 'supplier.address.postal' => '20000',
				'supplier.address.city' => 'Test town', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo2@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			],
		],
		'text' => [
			[
				'text.label' => 'Demo name/de', 'text.content' => 'Ballroom',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			],
			[
				'text.label' => 'Demo short/de',
				'text.content' => 'Stilvolle und modische Bekleidung, nachhaltig produziert',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			],
			[
				'text.label' => 'Demo long/de',
				'text.content' => 'Ballroom ist eine internationale Modemarke, bekannt für
					ihre stilvolle und moderne Bekleidung, Accessoires und Schuhe für Frauen,
					Männer und Kinder. Die Marke ist für ihre nachhaltigen Modekollektionen
					bekannt und setzt sich für Umweltschutz, soziale Verantwortung und faire
					Arbeitsbedingungen in der Modeindustrie ein.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			],
			[
				'text.label' => 'Demo name/en', 'text.content' => 'Ballroom',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			],
			[
				'text.label' => 'Demo short/en',
				'text.content' => 'Stylish and fashionable clothing, sustainably produced',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			],
			[
				'text.label' => 'Demo long/en',
				'text.content' => 'Ballroom is an international fashion brand, known for its
					its stylish and modern clothing, accessories and footwear for women, men
					and children. The brand is known for its sustainable fashion collections
					sustainable fashion collections and is committed to environmental protection,
					social responsibility and fair working conditions in the fashion industry.',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			],
			[
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: Supplier logo 4', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-4.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-4.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			],
		],
	],
];
