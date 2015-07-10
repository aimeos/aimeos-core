<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array (
	'locale/site' => array (
		'unittest' => array ( 'code' => 'unittest', 'label' => 'Unit test site', 'config' => array ("timezone" => "Europe/Berlin", "emailfrom" => "no-reply@metaways.de", "emailreply" => "eshop@metaways.de"), 'status' => 0 )
	),

	'locale' => array (
		array( 'siteid' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'pos' => 0, 'status' => 0 ),
		array( 'siteid' => 'unittest', 'langid' => 'en', 'currencyid' => 'EUR', 'pos' => 1, 'status' => 1 ),
	),

	'locale/currency' => array(
		'EUR' => array( 'id' => 'EUR', 'label' => 'Euro', 'status' => 1 ),
		'CHF' => array( 'id' => 'CHF', 'label' => 'Swiss franc', 'status' => 0 ),
		'USD' => array( 'id' => 'USD', 'label' => 'US dollar', 'status' => 1 ),
		'XAF' => array( 'id' => 'XAF', 'label' => 'CFA Franc BEAC', 'status' => 0 ),
		'XOF' => array( 'id' => 'XOF', 'label' => 'CFA Franc BCEAO', 'status' => 0 ),
	),

	'locale/language' => array(
		'de' => array( 'id' => 'de', 'label' => 'German', 'status' => 1 ),
		'en' => array( 'id' => 'en', 'label' => 'English', 'status' => 1 ),
		'es' => array( 'id' => 'es', 'label' => 'Spanish', 'status' => 1 ),
		'it' => array( 'id' => 'it', 'label' => 'Italian', 'status' => 0 ),
	),
);
