<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(

	// Single article
	array(
		'code' => 'demo-article', 'type' => 'default', 'label' => 'Demo article',
		'start' => null, 'end' => null, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Demoartikel',
				'content' => 'Demoartikel',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'content' => 'Dies ist die Kurzbeschreibung des Demoartikels',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'content' => 'Hier folgt eine ausführliche Beschreibung des Artikels, die gerne etwas länger sein darf.',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: This is the short description',
				'content' => 'This is the short description of the demo article.',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Add a detailed description',
				'content' => 'Add a detailed description of the demo article that may be a little bit longer.',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Article from 1',
				'value' => '100.00', 'costs' => '5.00', 'rebate' => '20.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article from 5',
				'value' => '90.00', 'costs' => '5.00', 'rebate' => '30.00', 'taxrate' => '20.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article from 10',
				'value' => '80.00', 'costs' => '5.00', 'rebate' => '40.00', 'taxrate' => '20.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article from 1',
				'value' => '130.00', 'costs' => '7.50', 'rebate' => '30.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article from 5',
				'value' => '120.00', 'costs' => '7.50', 'rebate' => '40.00', 'taxrate' => '10.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article from 10',
				'value' => '100.00', 'costs' => '7.50', 'rebate' => '60.00', 'taxrate' => '10.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 5, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: Article 1.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/1-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/1.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article 2.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/2-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/2.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article 3.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/3-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/3.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Article 4.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/4-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/4.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'attribute' => array(
			array(
				'code' => 'demo-black', 'label' => 'Demo: Black',
				'type' => 'color', 'position' => 1, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Schwarz',
						'content' => 'Schwarz',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Black',
						'content' => 'Black',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Schwarz',
						'content' => 'Schwarz',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Black',
						'content' => 'Black',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'media' => array(
					array(
						'label' => 'Demo: black.gif', 'mimetype' => 'image/gif',
						'url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=',
						'preview' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=',
						'type' => 'default', 'languageid' => null, 'status' => 1,
						'list-type' => 'icon', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-print-small', 'label' => 'Demo: Small print',
				'type' => 'option', 'position' => 0, 'status' => 1,
				'list-type' => 'config', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Kleiner Aufdruck',
						'content' => 'Kleiner Aufdruck',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Small print',
						'content' => 'Small print',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Kleiner Aufdruck',
						'content' => 'Kleiner_Aufdruck',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Small print',
						'content' => 'small_print',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'price' => array(
					array(
						'label' => 'Demo: Small print',
						'value' => '5.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo: Small print',
						'value' => '7.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-print-large', 'label' => 'Demo: Large print',
				'type' => 'option', 'position' => 1, 'status' => 1,
				'list-type' => 'config', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Grosser Aufdruck',
						'content' => 'Grosser Aufdruck',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Large print',
						'content' => 'Large print',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Grosser Aufdruck',
						'content' => 'Grosser_Aufdruck',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Large print',
						'content' => 'large_print',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'price' => array(
					array(
						'label' => 'Demo: Large print',
						'value' => '15.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo: Large print',
						'value' => '20.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-print-text', 'label' => 'Demo: Text for print',
				'type' => 'text', 'position' => 0, 'status' => 1,
				'list-type' => 'custom', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo url/de: Kleiner Aufdruck',
						'content' => 'Text_Aufdruck',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Small print',
						'content' => 'print_text',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
		),
		'property' => array(
			array( 'type' => 'package-length', 'languageid' => null, 'value' => '20.00' ),
			array( 'type' => 'package-width', 'languageid' => null, 'value' => '10.00' ),
			array( 'type' => 'package-height', 'languageid' => null, 'value' => '5.00' ),
			array( 'type' => 'package-weight', 'languageid' => null, 'value' => '2.5' ),
		),
		'stock' => array(
			array( 'stocklevel' => null, 'typeid' => 'default', 'dateback' => null )
		),
	),

	// Selection articles
	array(
		'code' => 'demo-selection-article-1', 'type' => 'default', 'label' => 'Demo variant article 1',
		'start' => null, 'end' => null, 'status' => 1,
		'attribute' => array(
			array(
				'code' => 'demo-blue', 'label' => 'Demo: Blue',
				'type' => 'color', 'position' => 2, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Blau',
						'content' => 'Blau',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Blue',
						'content' => 'Blue',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Blau',
						'content' => 'Blau',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Blue',
						'content' => 'blue',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'media' => array(
					array(
						'label' => 'Demo: blue.gif', 'mimetype' => 'image/gif',
						'url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAA/wAAACwAAAAAAQABAAACAkQBADs=',
						'preview' => 'data:image/gif;base64,R0lGODdhAQABAIAAAAAA/wAAACwAAAAAAQABAAACAkQBADs=',
						'type' => 'default', 'languageid' => null, 'status' => 1,
						'list-type' => 'icon', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-width-32', 'label' => 'Demo: Width 32',
				'type' => 'width', 'position' => 0, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name: Width 32', 'content' => '32',
						'type' => 'name', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Width 32', 'content' => 'Weite_32',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Width 32', 'content' => 'width_32',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-length-34', 'label' => 'Demo: Length 34',
				'type' => 'length', 'position' => 0, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name: Length 34', 'content' => '34',
						'type' => 'name', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Length 34', 'content' => 'Länge_34',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Length 34', 'content' => 'length_34',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
		),
		'stock' => array(
			array( 'stocklevel' => 3, 'typeid' => 'default', 'dateback' => null ),
		),
	),
	array(
		'code' => 'demo-selection-article-2', 'type' => 'default', 'label' => 'Demo variant article 2',
		'start' => null, 'end' => null, 'status' => 1,
		'attribute' => array(
			array(
				'code' => 'demo-beige', 'label' => 'Demo: Beige',
				'type' => 'color', 'position' => 0, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Beige', 'content' => 'Beige',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Beige', 'content' => 'Beige',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Beige', 'content' => 'Beige',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Beige', 'content' => 'beige',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'media' => array(
					array(
						'label' => 'Demo: beige.gif', 'mimetype' => 'image/gif',
						'url' => 'data:image/gif;base64,R0lGODdhAQABAIAAAPX13AAAACwAAAAAAQABAAACAkQBADs=',
						'preview' => 'data:image/gif;base64,R0lGODdhAQABAIAAAPX13AAAACwAAAAAAQABAAACAkQBADs=',
						'type' => 'default', 'languageid' => null, 'status' => 1,
						'list-type' => 'icon', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-width-33', 'label' => 'Demo: Width 33',
				'type' => 'width', 'position' => 1, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name: Width 33', 'content' => '33',
						'type' => 'name', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Width 33', 'content' => 'Weite_33',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Width 33', 'content' => 'width_33',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-length-36', 'label' => 'Demo: Length 36',
				'type' => 'length', 'position' => 1, 'status' => 1,
				'list-type' => 'variant', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name: Length 36', 'content' => '36',
						'type' => 'name', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Length 36', 'content' => 'Länge_36',
						'type' => 'url', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url: Length 36', 'content' => 'length_36',
						'type' => 'url', 'languageid' => null, 'status' => 1,
						'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
		),
		'stock' => array(
			array( 'stocklevel' => 0, 'typeid' => 'default', 'dateback' => '2015-01-01 12:00:00' ),
		),
	),
	array(
		'code' => 'demo-selection-article', 'type' => 'select', 'label' => 'Demo selection article',
		'supplier' => '', 'start' => null, 'end' => null, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Demoartikel mit Auswahl',
				'content' => 'Demoartikel mit Auswahl',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo url/de: Demoartikel mit Auswahl',
				'content' => 'Demoartikel_mit_Auswahl',
				'type' => 'url', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'content' => 'Dies ist die Kurzbeschreibung des Demoartikels mit Auswahl',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'content' => 'Hier folgt eine ausführliche Beschreibung des Artikels mit Auswahl, die gerne etwas länger sein darf.',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: This is the short description',
				'content' => 'This is the short description of the selection demo article.',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Add a detailed description',
				'content' => 'Add a detailed description of the selection demo article that may be a little bit longer.',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Selection article from 1',
				'value' => '150.00', 'costs' => '10.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article from 5',
				'value' => '135.00', 'costs' => '10.00', 'rebate' => '15.00', 'taxrate' => '10.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article from 10',
				'value' => '120.00', 'costs' => '10.00', 'rebate' => '30.00', 'taxrate' => '10.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article from 1',
				'value' => '200.00', 'costs' => '15.00', 'rebate' => '0.00', 'taxrate' => '5.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article from 5',
				'value' => '175.00', 'costs' => '15.00', 'rebate' => '25.00', 'taxrate' => '5.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article from 10',
				'value' => '150.00', 'costs' => '15.00', 'rebate' => '50.00', 'taxrate' => '5.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 5, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: Selection article 1.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/1-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/1.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article 2.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/2-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/2.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article 3.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/3-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/3.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Selection article 4.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/4-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/4.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'attribute' => array(
			array(
				'code' => 'demo-sticker-small', 'label' => 'Demo: Small sticker',
				'type' => 'option', 'position' => 2, 'status' => 1,
				'list-type' => 'config', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Kleines Etikett',
						'content' => 'Kleines Etikett',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Small sticker',
						'content' => 'Small sticker',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Kleines Etikett',
						'content' => 'Kleines_Etikett',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Small sticker',
						'content' => 'small_sticker',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'price' => array(
					array(
						'label' => 'Demo: Small sticker',
						'value' => '2.50', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo: Small sticker',
						'value' => '3.50', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '5.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
			array(
				'code' => 'demo-sticker-large', 'label' => 'Demo: Large sticker',
				'type' => 'option', 'position' => 3, 'status' => 1,
				'list-type' => 'config', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1,
				'text' => array(
					array(
						'label' => 'Demo name/de: Grosses Etikett',
						'content' => 'Grosses Etikett',
						'type' => 'name', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo name/en: Large sticker',
						'content' => 'Large sticker',
						'type' => 'name', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/de: Grosses Etikett',
						'content' => 'Grosses_Etikett',
						'type' => 'url', 'languageid' => 'de', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo url/en: Large sticker',
						'content' => 'large_sticker',
						'type' => 'url', 'languageid' => 'en', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
				'price' => array(
					array(
						'label' => 'Demo: Large sticker',
						'value' => '5.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
						'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
					array(
						'label' => 'Demo: Large sticker',
						'value' => '7.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '5.00',
						'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
						'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
						'list-start' => null, 'list-end' => null, 'list-status' => 1
					),
				),
			),
		),
		'product' => array(
			array(
				'code' => 'demo-selection-article-1',
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'code' => 'demo-selection-article-2',
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'code' => 'demo-article',
				'list-type' => 'suggestion', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'code' => 'demo-article',
				'list-type' => 'bought-together', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'stock' => array(
			array( 'stocklevel' => 3, 'typeid' => 'default', 'dateback' => null )
		),
	),

	// bundle article
	array(
		'code' => 'demo-bundle-article', 'type' => 'bundle', 'label' => 'Demo bundle article',
		'start' => null, 'end' => null, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Demoartikel mit Bundle',
				'content' => 'Demoartikel mit Bundle',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo url/de: Demoartikel mit Bundle',
				'content' => 'Demoartikel_mit_Bundle',
				'type' => 'url', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Dies ist die Kurzbeschreibung',
				'content' => 'Dies ist die Kurzbeschreibung des Demoartikels mit Bundle',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Hier folgt eine ausführliche Beschreibung',
				'content' => 'Hier folgt eine ausführliche Beschreibung des Artikels mit Bundle, die gerne etwas länger sein darf.',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: This is the short description',
				'content' => 'This is the short description of the bundle demo article.',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Add a detailed description',
				'content' => 'Add a detailed description of the bundle demo article that may be a little bit longer.',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Bundle article from 1',
				'value' => '250.00', 'costs' => '10.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article from 5',
				'value' => '235.00', 'costs' => '10.00', 'rebate' => '15.00', 'taxrate' => '10.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article from 10',
				'value' => '220.00', 'costs' => '10.00', 'rebate' => '30.00', 'taxrate' => '10.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article from 1',
				'value' => '250.00', 'costs' => '15.00', 'rebate' => '0.00', 'taxrate' => '5.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article from 5',
				'value' => '225.00', 'costs' => '15.00', 'rebate' => '25.00', 'taxrate' => '5.00',
				'quantity' => 5, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article from 10',
				'value' => '200.00', 'costs' => '15.00', 'rebate' => '50.00', 'taxrate' => '5.00',
				'quantity' => 10, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 5, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: Bundle article 1.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/1-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/1.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article 2.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/2-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/2.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article 3.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/3-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/3.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Bundle article 4.jpg', 'mimetype' => 'image/jpeg',
				'url' => 'http://demo.aimeos.org/media/4-big.jpg',
				'preview' => 'http://demo.aimeos.org/media/4.jpg',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'attribute' => array(
		),
		'product' => array(
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
		),
		'stock' => array(
			array( 'stocklevel' => 0, 'typeid' => 'default', 'dateback' => null ),
		),
	),
	array(
		'code' => 'demo-rebate', 'type' => 'default', 'label' => 'Demo rebate',
		'start' => null, 'end' => null, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Rabatt',
				'content' => 'Demorabatt',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'stock' => array(
			array( 'stocklevel' => null, 'typeid' => 'default', 'dateback' => null )
		),
	),
);
