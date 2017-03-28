<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	),
	'media' => array(
		array(
			'label' => 'Demo: Home stage image', 'mimetype' => 'image/png',
			'url' => 'http://demo.aimeos.org/media/stage.jpg',
			'preview' => 'http://demo.aimeos.org/media/stage.jpg',
			'type' => 'default', 'languageid' => null, 'status' => 1,
			'list-type' => 'stage', 'list-position' => 0, 'list-config' => [],
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
			'code' => 'demo-selection-article',
			'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-article',
			'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
		array(
			'code' => 'demo-bundle-article',
			'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
			'list-start' => null, 'list-end' => null, 'list-status' => 1
		),
	),
);
