<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	array(
		'code' => 'demo-test', 'label' => 'Test user', 'salutation' => 'mr',
		'company' => 'Test company', 'vatno' => 'DE999999999', 'title' => '', 'firstname' => 'Test', 'lastname' => 'User',
		'address1' => 'Test street', 'address2' => '1', 'address3' => '', 'postal' => '1000',
		'city' => 'Test city', 'state' => '', 'langid' => 'en', 'countryid' => 'DE',
		'telephone' => '', 'email' => 'me@localhost', 'telefax' => '', 'website' => '',
		'birthday' => null, 'password' => sha1( microtime(true) . getmypid() . rand() ),
		'vtime' => null, 'status' => 1,
	),
);
