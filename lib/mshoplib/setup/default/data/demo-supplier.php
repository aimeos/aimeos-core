<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */

return array(
	array(
		'code' => 'demo-test1', 'label' => 'Test supplier 1', 'status' => 1,
		'delivery' => array(
			array(
				'salutation' => 'company', 'company' => 'Test company', 'vatid' => 'DE999999999', 'title' => '',
				'firstname' => '', 'lastname' => '', 'address1' => 'Test street', 'address2' => '1', 'address3' => '',
				'postal' => '10000', 'city' => 'Test city', 'state' => 'NY', 'langid' => 'en', 'countryid' => 'US',
				'telephone' => '', 'email' => 'demo1@example.com', 'telefax' => '', 'website' => '',
			),
		),
		'product' => array(
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
		),
	),
	array(
		'code' => 'demo-test2', 'label' => 'Test supplier 2', 'status' => 1,
		'delivery' => array(
			array(
				'salutation' => 'company', 'company' => 'Test company', 'vatid' => 'DE999999999', 'title' => '',
				'firstname' => '', 'lastname' => '', 'address1' => 'Test road', 'address2' => '10', 'address3' => '',
				'postal' => '20000', 'city' => 'Test town', 'state' => 'NY', 'langid' => 'en', 'countryid' => 'US',
				'telephone' => '', 'email' => 'demo2@example.com', 'telefax' => '', 'website' => '',
			),
		),
		'product' => array(
			array(
				'code' => 'demo-selection-article',
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'code' => 'demo-bundle-article',
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
);
