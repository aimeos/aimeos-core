<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return array(
	'code' => 'home', 'label' => 'Home', 'config' => [], 'status' => 1,
	'text' => array(
		array(
			'label' => 'Demo name/de: Start', 'content' => 'Start',
			'type' => 'name', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'label' => 'Demo url/de: Start', 'content' => 'Start',
			'type' => 'url', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'label' => 'Demo short/de: Dies ist der Kurztext',
			'content' => 'Dies ist der Kurztext für die Hauptkategorie ihres neuen Webshops.',
			'type' => 'short', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'label' => 'Demo short/en: This is the short text',
			'content' => 'This is the short text for the main category of your new web shop.',
			'type' => 'short', 'languageid' => 'en', 'status' => 1,
			'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'label' => 'Demo long/de: Hier kann eine ausführliche Einleitung',
			'content' => 'Hier kann eine ausführliche Einleitung für ihre Hauptkategorie stehen.',
			'type' => 'long', 'languageid' => 'de', 'status' => 1,
			'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'label' => 'Demo long/en: Here you can add a long introduction',
			'content' => 'Here you can add a long introduction for you main category.',
			'type' => 'long', 'languageid' => 'en', 'status' => 1,
			'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'text.label' => 'Demo meta-description',
			'text.content' => 'Meta descriptions are important because they are shown in the search engine result page',
			'text.type' => 'long', 'text.languageid' => null, 'text.status' => 1,
			'product.lists.type' => 'default', 'product.lists.position' => 6
		),
	),
	'media' => array(
		array(
			'label' => 'Demo: Home stage image', 'mimetype' => 'image/png',
			'url' => 'https://demo.aimeos.org/media/stage-big.jpg',
			'preview' => [
				360 => 'https://demo.aimeos.org/media/stage.jpg',
				720 => 'https://demo.aimeos.org/media/stage-med.jpg',
				1200 => 'https://demo.aimeos.org/media/stage-big.jpg',
			],
			'type' => 'stage', 'languageid' => null, 'status' => 1,
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
	),
	'product' => array(
		array(
			'code' => 'demo-article',
			'list-type' => 'promotion', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-article',
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-selection-article',
			'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-bundle-article',
			'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-voucher',
			'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
	),
);
