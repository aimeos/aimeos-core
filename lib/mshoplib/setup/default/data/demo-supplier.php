<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */

return array(
	array(
		'supplier.code' => 'demo-test1', 'supplier.label' => 'Test supplier 1', 'supplier.status' => 1,
		'address' => array(
			array(
				'supplier.address.salutation' => 'company', 'company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test street', 'supplier.address.address2' => '1',
				'supplier.address.address3' => '', 'supplier.address.postal' => '10000',
				'supplier.address.city' => 'Test city', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo1@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			),
		),
		'text' => array(
			array(
				'text.label' => 'Demo name/de: Demo Händler', 'text.content' => 'Demo-Händler',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			),
			array(
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demo-Händlers',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			),
			array(
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Lieferanten, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			),
			array(
				'text.label' => 'Demo name/en: Demo supplier', 'text.content' => 'Demo supplier',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			),
			array(
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo supplier.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			),
			array(
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo supplier that may be a little bit longer.',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			),
			array(
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			),
		),
		'media' => array(
			array(
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-1.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-1.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			),
		),
		'product' => array(
			array(
				'product.code' => 'demo-article',
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1
			),
			array(
				'product.code' => 'demo-selection-article',
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1
			),
		),
	),
	array(
		'supplier.code' => 'demo-test2', 'supplier.label' => 'Test supplier 2', 'supplier.status' => 1,
		'address' => array(
			array(
				'supplier.address.salutation' => 'company', 'supplier.address.company' => 'Test company',
				'supplier.address.vatid' => 'DE999999999', 'supplier.address.title' => '',
				'supplier.address.firstname' => '', 'supplier.address.lastname' => '',
				'supplier.address.address1' => 'Test road', 'supplier.address.address2' => '10',
				'supplier.address.address3' => '', 'supplier.address.postal' => '20000',
				'supplier.address.city' => 'Test town', 'supplier.address.state' => 'NY',
				'supplier.address.langid' => 'en', 'supplier.address.countryid' => 'US',
				'supplier.address.telephone' => '', 'supplier.address.email' => 'demo2@example.com',
				'supplier.address.telefax' => '', 'supplier.address.website' => '',
			),
		),
		'text' => array(
			array(
				'text.label' => 'Demo name/de: Demo Händler 2', 'text.content' => 'Demo-Händler 2',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0
			),
			array(
				'text.label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'text.content' => 'Dies ist die Kurzbeschreibung des Demo-Händlers 2',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1
			),
			array(
				'text.label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'text.content' => 'Hier folgt eine ausführliche Beschreibung des Lieferanten, die gerne etwas länger sein darf.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 2
			),
			array(
				'text.label' => 'Demo name/en: Demo supplier 2', 'text.content' => 'Demo supplier 2',
				'text.type' => 'name', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 3
			),
			array(
				'text.label' => 'Demo short/en: This is the short description',
				'text.content' => 'This is the short description of the demo article.',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 4
			),
			array(
				'text.label' => 'Demo long/en: Add a detailed description',
				'text.content' => 'Add a detailed description of the demo article that may be a little bit longer.',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 5
			),
			array(
				'text.label' => 'Demo meta-description',
				'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
				'text.type' => 'meta-description', 'text.languageid' => null, 'text.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 6
			),
		),
		'media' => array(
			array(
				'media.label' => 'Demo: Article 1.jpg', 'media.mimetype' => 'image/jpeg',
				'media.url' => 'https://aimeos.org/media/default/logo-4.png',
				'media.previews' => [240 => 'https://aimeos.org/media/default/logo-4.png'],
				'media.type' => 'default', 'media.languageid' => null, 'media.status' => 1,
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1,
			),
		),
		'product' => array(
			array(
				'product.code' => 'demo-selection-article',
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 0, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1
			),
			array(
				'product.code' => 'demo-bundle-article',
				'supplier.lists.type' => 'default', 'supplier.lists.position' => 1, 'supplier.lists.config' => [],
				'supplier.lists.start' => null, 'supplier.lists.end' => null, 'supplier.lists.status' => 1
			),
		),
	),
);
