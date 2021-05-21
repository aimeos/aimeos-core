<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org], 2015-2021
 */

return [
	'locale/site' => [
		'unittest' => [
			'code' => 'unittest', 'label' => 'Unit test site', 'status' => 1, 'theme' => 'shop',
			'icon' => 'path/to/site-icon.png', 'logo' => [1 => 'path/to/site-logo.png'], 'supplierid' => '1234',
			'config' => ["timezone" => "Europe/Berlin", "emailfrom" => "no-reply@example.com", "emailreply" => "test@example.com"]
		]
	],

	'locale/currency' => [
		'EUR' => ['id' => 'EUR', 'label' => 'Euro', 'status' => 1],
		'CHF' => ['id' => 'CHF', 'label' => 'Swiss franc', 'status' => 0],
		'USD' => ['id' => 'USD', 'label' => 'US dollar', 'status' => 1],
		'XAF' => ['id' => 'XAF', 'label' => 'CFA Franc BEAC', 'status' => 0],
		'XOF' => ['id' => 'XOF', 'label' => 'CFA Franc BCEAO', 'status' => 0],
	],

	'locale/language' => [
		'de' => ['id' => 'de', 'label' => 'German', 'status' => 1],
		'en' => ['id' => 'en', 'label' => 'English', 'status' => 1],
		'es' => ['id' => 'es', 'label' => 'Spanish', 'status' => 1],
		'it' => ['id' => 'it', 'label' => 'Italian', 'status' => 0],
	],

	'locale' => [
		['siteid' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'pos' => 0, 'status' => 0],
		['siteid' => 'unittest', 'langid' => 'en', 'currencyid' => 'EUR', 'pos' => 1, 'status' => 1],
	],
];
