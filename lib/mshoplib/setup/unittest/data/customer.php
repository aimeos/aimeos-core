<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'customer' => array(
		'customer/UTC001' => array( 'label' => 'unitCustomer001', 'code' => 'UTC001', 'status' => 1, 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => 'Dr', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.0', 'latitude' => '50.0', 'password' => 'unittest' ),
		'customer/UTC002' => array( 'label' => 'unitCustomer002', 'code' => 'UTC002', 'status' => 1, 'company' => '', 'vatid' => '', 'salutation' => '', 'title' => '', 'firstname' => '', 'lastname' => '', 'address1' => '', 'address2' => '', 'address3' => '', 'postal' => '', 'city' => '', 'state' => '', 'countryid' => null, 'langid' => 'de', 'telephone' => '', 'email' => '', 'telefax' => '', 'website' => '', 'longitude' => '', 'latitude' => '' ),
		'customer/UTC003' => array( 'label' => 'unitCustomer003', 'code' => 'UTC003', 'status' => 0, 'company' => '', 'vatid' => '', 'salutation' => '', 'title' => '', 'firstname' => '', 'lastname' => '', 'address1' => '', 'address2' => '', 'address3' => '', 'postal' => '', 'city' => '', 'state' => '', 'countryid' => null, 'langid' => 'de', 'telephone' => '', 'email' => '', 'telefax' => '', 'website' => '', 'longitude' => '', 'latitude' => '' ),
	),

	'customer/address' => array(
		array( 'parentid' => 'customer/UTC001', 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => 'Dr', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.0', 'latitude' => '50.0', 'flag' => 0, 'pos' => '0' ),
		array( 'parentid' => 'customer/UTC002', 'company' => 'Example company LLC', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Good', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.5', 'latitude' => '51.0', 'flag' => 0, 'pos' => '1' ),
		array( 'parentid' => 'customer/UTC002', 'company' => 'Example company LLC', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Good', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '11099', 'city' => 'Berlin', 'state' => 'Berlin', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '055544332221', 'email' => 'test@example.com', 'telefax' => '055544333212', 'website' => 'www.example.com', 'longitude' => '11.0', 'latitude' => '52.0', 'flag' => 0, 'pos' => '1' ),
		array( 'parentid' => 'customer/UTC003', 'company' => 'unitcompany', 'vatid' => 'vatnumber', 'salutation' => 'company', 'title' => 'unittitle', 'firstname' => 'unitfirstname', 'lastname' => 'unitlastname', 'address1' => 'unitaddress1', 'address2' => 'unitaddress2', 'address3' => 'unitaddress3', 'postal' => 'unitpostal', 'city' => 'unitcity', 'state' => 'unitstate', 'countryid' => 'DE', 'langid' => 'de', 'telephone' => '1234567890', 'email' => 'unit@email', 'telefax' => '1234567891', 'website' => 'unit.web.site', 'longitude' => '10.0', 'latitude' => '53.5', 'flag' => 0, 'pos' => '2' ),
	),

	'customer/group' => array(
		'customer/group/unitgroup' => array( 'code' => 'unitgroup', 'label' => 'Unitgroup' ),
	),
);