<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org], 2015-2023
 */

return [
	'locale/site' => [
		'unittest' => [
			'locale.site.code' => 'unittest', 'locale.site.label' => 'Unit test site', 'locale.site.theme' => 'shop',
			'locale.site.icon' => 'path/to/site-icon.png', 'locale.site.logo' => [1 => 'path/to/site-logo.png'],
			'locale.site.refid' => '1234',
			'locale.site.config' => [
				"timezone" => "Europe/Berlin",
				"emailfrom" => "no-reply@example.com",
				"emailreply" => "test@example.com"
			]
		]
	],

	'locale/currency' => [
		'EUR' => ['locale.currency.id' => 'EUR', 'locale.currency.label' => 'Euro', 'locale.currency.status' => 1],
		'CHF' => ['locale.currency.id' => 'CHF', 'locale.currency.label' => 'Swiss franc', 'locale.currency.status' => 0],
		'USD' => ['locale.currency.id' => 'USD', 'locale.currency.label' => 'US dollar', 'locale.currency.status' => 1],
		'XAF' => ['locale.currency.id' => 'XAF', 'locale.currency.label' => 'CFA Franc BEAC', 'locale.currency.status' => 0],
		'XOF' => ['locale.currency.id' => 'XOF', 'locale.currency.label' => 'CFA Franc BCEAO', 'locale.currency.status' => 0],
	],

	'locale/language' => [
		'de' => ['locale.language.id' => 'de', 'locale.language.label' => 'German', 'locale.language.status' => 1],
		'en' => ['locale.language.id' => 'en', 'locale.language.label' => 'English', 'locale.language.status' => 1],
		'es' => ['locale.language.id' => 'es', 'locale.language.label' => 'Spanish', 'locale.language.status' => 1],
		'it' => ['locale.language.id' => 'it', 'locale.language.label' => 'Italian', 'locale.language.status' => 0],
	],

	'locale' => [
		['site' => 'unittest', 'locale.languageid' => 'de', 'locale.currencyid' => 'EUR', 'locale.position' => 0, 'locale.status' => 0],
		['site' => 'unittest', 'locale.languageid' => 'en', 'locale.currencyid' => 'EUR', 'locale.position' => 1, 'locale.status' => 1],
	],
];
