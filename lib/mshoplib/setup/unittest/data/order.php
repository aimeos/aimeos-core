<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return array(
	'order/base' => array(
		'19.95' => array( 'customerid' => 'test@example.com', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '19.95', 'shipping' => '6.50', 'rebate' => '0.00', 'customerref' => 'ABC-1234', 'comment' => 'This is a comment if an order. It can be added by the user.' ),
		'636.00' => array( 'customerid' => 'test@example.com', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '636.00', 'shipping' => '31.00', 'rebate' => '0.00', 'customerref' => 'ABC-9876', 'comment' => 'This is another comment.' ),
		'18.00' => array( 'customerid' => 'test@example.com', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '18.00', 'shipping' => '1.00', 'rebate' => '0.00', 'customerref' => 'XYZ-1234', 'comment' => 'This is a bundle basket.' ),
		'10.00' => array( 'customerid' => 'test@example.com', 'sitecode' => 'unittest', 'langid' => 'de', 'currencyid' => 'EUR', 'price' => '10.00', 'shipping' => '4.50', 'rebate' => '2.00', 'customerref' => 'XYZ-9876', 'comment' => 'This is a comment if an order. It can be added by the user.' ),
	),

	'order/base/address' => array(
		array( 'baseid' => '19.95', 'addrid' => 101, 'type' => 'delivery', 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => 'Dr.', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.0', 'latitude' => '50.0', 'flag' => null ),
		array( 'baseid' => '636.00', 'addrid' => 102, 'type' => 'delivery', 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mrs', 'title' => 'Dr.', 'firstname' => 'Maria', 'lastname' => 'Mustertest', 'address1' => 'Pickhuben', 'address2' => '2', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.5', 'latitude' => '51.0', 'flag' => null ),
		array( 'baseid' => '19.95', 'addrid' => 103, 'type' => 'payment', 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mr', 'title' => '', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Durchschnitt', 'address2' => '1', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332213', 'website' => 'www.example.net', 'longitude' => '11.0', 'latitude' => '52.0', 'birthday' => '2001-01-01', 'flag' => null ),
		array( 'baseid' => '636.00', 'addrid' => 104, 'type' => 'payment', 'company' => null, 'vatid' => null, 'salutation' => 'mrs', 'title' => '', 'firstname' => 'Adelheid', 'lastname' => 'Mustertest', 'address1' => 'Königallee', 'address2' => '1', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332213', 'website' => 'www.example.net', 'longitude' => '10.0', 'latitude' => '50.0', 'flag' => null ),
		array( 'baseid' => '10.00', 'addrid' => 105, 'type' => 'delivery', 'company' => 'Example company', 'vatid' => 'DE999999999', 'salutation' => 'mrs', 'title' => 'Dr.', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Pickhuben', 'address2' => '2-4', 'address3' => '', 'postal' => '20457', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332212', 'email' => 'test@example.com', 'telefax' => '055544332212', 'website' => 'www.example.com', 'longitude' => '10.5', 'latitude' => '51.0', 'flag' => null ),
		array( 'baseid' => '10.00', 'addrid' => 106, 'type' => 'payment', 'company' => null, 'vatid' => null, 'salutation' => 'mr', 'title' => '', 'firstname' => 'Our', 'lastname' => 'Unittest', 'address1' => 'Durchschnitt', 'address2' => '2', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332212', 'email' => 'test@example.com', 'telefax' => '055544332213', 'website' => 'www.example.net', 'longitude' => '11.0', 'latitude' => '52.0', 'flag' => null ),
		array( 'baseid' => '18.00', 'addrid' => 107, 'type' => 'payment', 'company' => null, 'vatid' => null, 'salutation' => 'mrs', 'title' => '', 'firstname' => 'Adelheid', 'lastname' => 'Mustertest', 'address1' => 'Königallee', 'address2' => '1', 'address3' => '', 'postal' => '20146', 'city' => 'Hamburg', 'state' => 'Hamburg', 'countryid' => 'de', 'langid' => 'de', 'telephone' => '055544332211', 'email' => 'test@example.com', 'telefax' => '055544332213', 'website' => 'www.example.net', 'longitude' => '10.0', 'latitude' => '50.0', 'flag' => null ),
	),

	'order/base/product' => array(
		'CNE/19.95' => array( 'baseid' => '19.95', 'type'=> 'default', 'prodid' => 'CNE', 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'stocktype' => 'default', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump1.jpg', 'amount' => 9, 'currencyid' => 'EUR', 'price' => '4.50', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'flags' => '0', 'pos' => 1, 'status' => 1, 'timeframe' => '4-5d' ),
		'CNC/19.95' => array( 'baseid' => '19.95', 'type'=> 'default', 'prodid' => 'CNC', 'prodcode' => 'CNC', 'suppliercode' => 'unitsupplier', 'stocktype' => 'default', 'name' => 'Cafe Noire Cappuccino', 'mediaurl' => 'somewhere/thump2.jpg', 'amount' => 3, 'currencyid' => 'EUR', 'price' => '6.00', 'shipping' => '0.50', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'flags' => '0', 'pos' => 2, 'status' => 1 ),
		'U:MD/19.95' => array( 'baseid' => '19.95', 'type'=> 'default', 'prodid' => 'U:MD', 'prodcode' => 'U:MD', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type3', 'name' => 'Unittest: Monetary rebate', 'mediaurl' => 'somewhere/thump3.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '-5.00', 'shipping' => '0.00', 'rebate' => '5.00', 'taxrates' => ['' => '0.00'], 'flags' => '1', 'pos' => 3, 'status' => 1 ),
		'ABCD/19.95' => array( 'baseid' => '19.95', 'type'=> 'default', 'prodid' => 'ABCD', 'prodcode' => 'ABCD', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => '16 discs', 'mediaurl' => 'somewhere/thump4.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '4.50', 'taxrates' => ['' => '0.00'], 'flags' => '1', 'pos' => 4, 'status' => 1 ),
		'CNE/636.00' => array( 'baseid' => '636.00', 'type'=> 'default', 'prodid' => 'CNE', 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'stocktype' => 'default', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump5.jpg', 'amount' => 2, 'currencyid' => 'EUR', 'price' => '36.00', 'shipping' => '1.00', 'rebate' => '0.00', 'taxrates' => ['' => '19.00'], 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'CNC/636.00' => array( 'baseid' => '636.00', 'type'=> 'default', 'prodid' => 'CNC', 'prodcode' => 'CNC', 'suppliercode' => 'unitsupplier', 'stocktype' => 'default', 'name' => 'Cafe Noire Cappuccino', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '19.00'], 'flags' => '0', 'pos' => 2, 'status' => 1 ),
			// product bundle test data
		'bdl:zyx/18.00' => array( 'baseid' => '18.00', 'type'=> 'bundle', 'prodid' => 'bdl:zyx', 'ordprodid' => null, 'prodcode' => 'bdl:zyx', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '1200.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '17.00'], 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'bdl:EFG/18.00' => array( 'baseid' => '18.00', 'type'=> 'default', 'prodid' => 'bdl:EFG', 'ordprodid' => 'bdl:zyx/18.00', 'prodcode' => 'bdl:EFG', 'type'=> 'default', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '16.00'], 'flags' => '0', 'pos' => 2, 'status' => 1 ),
		'bdl:HIJ/18.00' => array( 'baseid' => '18.00', 'type'=> 'default', 'prodid' => 'bdl:HIJ', 'ordprodid' => 'bdl:zyx/18.00', 'prodcode' => 'bdl:HIJ', 'type'=> 'default', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest 1', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '17.00'], 'flags' => '0', 'pos' => 3, 'status' => 1 ),
		'bdl:hal/18.00' => array( 'baseid' => '18.00', 'type'=> 'bundle', 'prodid' => 'bdl:hal', 'ordprodid' => null, 'prodcode' => 'bdl:hal', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '1200.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '17.00'], 'flags' => '0', 'pos' => 4, 'status' => 1 ),
		'bdl:EFX/18.00' => array( 'baseid' => '18.00', 'type'=> 'default', 'prodid' => 'bdl:EFX', 'ordprodid' => 'bdl:hal/18.00', 'prodcode' => 'bdl:EFX', 'type'=> 'default', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest 2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '16.00'], 'flags' => '0', 'pos' => 5, 'status' => 1 ),
		'bdl:HKL/18.00' => array( 'baseid' => '18.00', 'type'=> 'default', 'prodid' => 'bdl:HKL', 'ordprodid' => 'bdl:hal/18.00', 'prodcode' => 'bdl:HKL', 'type'=> 'default', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => 'Bundle Unittest 2', 'mediaurl' => 'somewhere/thump6.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '600.00', 'shipping' => '30.00', 'rebate' => '0.00', 'taxrates' => ['' => '18.00'], 'flags' => '0', 'pos' => 6, 'status' => 1 ),
		'CNE/10.00' => array( 'baseid' => '10.00', 'type'=> 'default', 'prodid' => 'CNE', 'ordprodid' => null, 'prodcode' => 'CNE', 'suppliercode' => 'unitsupplier', 'stocktype' => 'default', 'name' => 'Cafe Noire Expresso', 'mediaurl' => 'somewhere/thump1.jpg', 'amount' => 3, 'currencyid' => 'EUR', 'price' => '4.50', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'flags' => '0', 'pos' => 1, 'status' => 1 ),
		'ABCD/10.00' => array( 'baseid' => '10.00', 'type'=> 'default', 'prodid' => 'ABCD', 'ordprodid' => null, 'prodcode' => 'ABCD', 'suppliercode' => 'unitsupplier', 'stocktype' => 'unit_type1', 'name' => '16 discs', 'mediaurl' => 'somewhere/thump4.jpg', 'amount' => 1, 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '4.50', 'taxrates' => ['' => '0.00'], 'flags' => '0', 'pos' => 2, 'status' => 1 ),
	),
	'order/base/product/attr' => array(
		array( 'ordprodid' => 'CNE/19.95', 'type' => 'default', 'code' => 'width', 'value' => 33, 'name' => '33', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNE/19.95', 'type' => 'default', 'code' => 'length', 'value' => 36, 'name' => '36', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNE/19.95', 'type' => 'config', 'code' => 'interval', 'value' => 'P0Y1M0W0D', 'name' => 'P0Y1M0W0D', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNC/19.95', 'type' => 'default', 'code' => 'size', 'value' => 's', 'name' => 'small', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNC/19.95', 'type' => 'default', 'code' => 'color', 'value' => 'blue', 'name' => 'blau', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNC/19.95', 'type' => 'config', 'code' => 'interval', 'value' => 'P1Y0M0W0D', 'name' => 'P1Y0M0W0D', 'quantity' => 1 ),
		array( 'ordprodid' => 'U:MD/19.95', 'type' => 'default', 'code' => 'size', 'value' => 's', 'name' => 'small', 'quantity' => 1 ),
		array( 'ordprodid' => 'U:MD/19.95', 'type' => 'default', 'code' => 'color', 'value' => 'white', 'name' => 'weiss', 'quantity' => 1 ),
		array( 'ordprodid' => 'ABCD/19.95', 'type' => 'default', 'code' => 'width', 'value' => 32, 'name' => '32', 'quantity' => 1 ),
		array( 'ordprodid' => 'ABCD/19.95', 'type' => 'default', 'code' => 'length', 'value' => 30, 'name' => '30', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNE/10.00', 'type' => 'default', 'code' => 'width', 'value' => 32, 'name' => '32', 'quantity' => 1 ),
		array( 'ordprodid' => 'CNE/10.00', 'type' => 'default', 'code' => 'length', 'value' => 36, 'name' => '36', 'quantity' => 1 ),
		array( 'ordprodid' => 'ABCD/10.00', 'type' => 'default', 'code' => 'width', 'value' => 32, 'name' => '32', 'quantity' => 1 ),
		array( 'ordprodid' => 'ABCD/10.00', 'type' => 'default', 'code' => 'length', 'value' => 30, 'name' => '30', 'quantity' => 1 ),
	),

	'order/base/service' => array(
		'OGONE/19.95' => array( 'baseid' => '19.95', 'servid' => 'unitpaymentcode', 'type' => 'payment', 'code' => 'OGONE', 'name' => 'ogone', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/19.95' => array( 'baseid' => '19.95', 'servid' => 'unitcode', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'OGONE/636.00' => array( 'baseid' => '636.00', 'servid' => 'unitpaymentcode', 'type' => 'payment', 'code' => 'OGONE', 'name' => 'ogone', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/636.00' => array( 'baseid' => '636.00', 'servid' => 'unitcode', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'paypal/10.00' => array( 'baseid' => '10.00', 'servid' => 'paypalexpress', 'type' => 'payment', 'code' => 'paypalexpress', 'name' => 'paypal', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/10.00' => array( 'baseid' => '10.00', 'servid' => 'unitcode', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'directdebit/18.00' => array( 'baseid' => '18.00', 'servid' => 'directdebit-test', 'type' => 'payment', 'code' => 'directdebit-test', 'name' => 'DirectDebit', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '0.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
		'solucia/18.00' => array( 'baseid' => '18.00', 'servid' => 'unitcode', 'type' => 'delivery', 'code' => 73, 'name' => 'solucia', 'currencyid' => 'EUR', 'price' => '0.00', 'shipping' => '5.00', 'rebate' => '0.00', 'taxrates' => ['' => '0.00'], 'mediaurl' => 'somewhere/thump1.jpg' ),
	),

	'order/base/service/attr' => array(
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'account owner', 'code' => 'ACOWNER', 'value' => 'test user', 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'account number', 'code' => 'ACSTRING', 'value' => 9876543, 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'payment method', 'code' => 'NAME', 'value' => 'CreditCard', 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'reference id', 'code' => 'REFID', 'value' => 12345678, 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'transaction date', 'code' => 'TXDATE', 'value' => '2009-08-18', 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'transaction account', 'code' => 'X-ACCOUNT', 'value' => 'Kraft02', 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'transaction status', 'code' => 'X-STATUS', 'value' => 9, 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'ogone alias name', 'code' => 'Ogone-alias-name', 'value' => 'aliasName', 'quantity' => 1 ),
		array( 'ordservid' => 'OGONE/19.95', 'type' => 'payment', 'name' => 'ogone alias value', 'code' => 'Ogone-alias-value', 'value' => 'aliasValue', 'quantity' => 1 ),
	),

	'order' => array(
		'2008-02-15 12:34:56' => array( 'baseid' => '19.95', 'type' => 'web', 'datepayment' => '2008-02-15 12:34:56', 'datedelivery' => null, 'statuspayment' => 6, 'statusdelivery' => 4, 'relatedid' => null ),
		'2009-09-17 16:14:32' => array( 'baseid' => '636.00', 'type' => 'phone', 'datepayment' => '2009-09-17 16:14:32', 'datedelivery' => null, 'statuspayment' => 6, 'statusdelivery' => 4, 'relatedid' => null ),
		'2011-03-27 11:11:14' => array( 'baseid' => '10.00', 'type' => 'web', 'datepayment' => '2011-09-17 16:14:32', 'datedelivery' => null, 'statuspayment' => 5, 'statusdelivery' => 3, 'relatedid' => null ),
		'2009-03-18 16:14:32' => array( 'baseid' => '18.00', 'type' => 'web', 'datepayment' => '2009-03-18 16:14:32', 'datedelivery' => null, 'statuspayment' => 6, 'statusdelivery' => 4, 'relatedid' => null ),
	),

	'order/status' => array(
		array( 'parentid' => '2008-02-15 12:34:56', 'type' => 'typestatus', 'value' => 'shipped' ),
		array( 'parentid' => '2009-09-17 16:14:32', 'type' => 'typestatus', 'value' => 'waiting' ),
		array( 'parentid' => '2011-03-27 11:11:14', 'type' => 'status', 'value' => 'waiting' ),
	)
);
