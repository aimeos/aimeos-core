<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'supplier' => array(
		'supplier/unitCode001' => array( 'label' => 'unitSupplier001', 'code' => 'unitCode001', 'status' => 1 ),
		'supplier/unitCode002' => array( 'label' => 'unitSupplier002', 'code' => 'unitCode002', 'status' => 1 ),
		'supplier/unitCode003' => array( 'label' => 'unitSupplier003', 'code' => 'unitCode003', 'status' => 0 ),
	),

	'supplier/address' => array(
		array( 'refid' => 'supplier/unitCode001', 'company' => 'Metaways', 'vatid' => 'DE999999999', 'salutation' => 'mrs', 'title' => '', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'pos' => '0' ),
		array( 'refid' => 'supplier/unitCode002', 'company' => 'Metaways GmbH', 'vatid' => 'DE999999999', 'salutation' => 'mrs', 'title' => '', 'firstname' => 'Good', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'eshop@metaways.de', 'telefax' => '055544332212', 'website' => 'www.metaways.de', 'pos' => '1' ),
		array( 'refid' => 'supplier/unitCode003', 'company' => 'unitcompany', 'vatid' => 'vatnumber', 'salutation' => 'company', 'title' => 'unittitle', 'firstname' => 'unitfirstname', 'lastname' => 'unitlastname', 'address1' => 'unitaddress1', 'address2' => 'unitaddress2', 'address3' => 'unitaddress3', 'postal' => 'unitpostal', 'city' => 'unitcity', 'state' => 'unitstate', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '1234567890', 'email' => 'unit@email', 'telefax' => '1234567891', 'website' => 'unit.web.site', 'pos' => '2' ),
	)
);