<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'supplier/lists/type' => [
		['supplier.lists.type.domain' => 'attribute', 'supplier.lists.type.code' => 'default', 'supplier.lists.type.label' => 'Standard', 'supplier.lists.type.status' => 1],
		['supplier.lists.type.domain' => 'media', 'supplier.lists.type.code' => 'default', 'supplier.lists.type.label' => 'Standard', 'supplier.lists.type.status' => 1],
		['supplier.lists.type.domain' => 'product', 'supplier.lists.type.code' => 'default', 'supplier.lists.type.label' => 'Standard', 'supplier.lists.type.status' => 1],
		['supplier.lists.type.domain' => 'text', 'supplier.lists.type.code' => 'default', 'supplier.lists.type.label' => 'Standard', 'supplier.lists.type.status' => 1],
	],

	'supplier' => [[
		'supplier.label' => 'Unit Supplier 001', 'supplier.code' => 'unitSupplier001', 'supplier.status' => 1,
		'address' => [[
			'supplier.address.company' => 'Example company', 'supplier.address.vatid' => 'DE999999999',
			'supplier.address.salutation' => 'mrs', 'supplier.address.title' => '',
			'supplier.address.firstname' => 'Our', 'supplier.address.lastname' => 'Unittest',
			'supplier.address.address1' => 'Pickhuben', 'supplier.address.address2' => '2',
			'supplier.address.address3' => '', 'supplier.address.postal' => '20457',
			'supplier.address.city' => 'Hamburg', 'supplier.address.state' => 'Hamburg',
			'supplier.address.countryid' => 'de', 'supplier.address.languageid' => 'de',
			'supplier.address.telephone' => '055544332211', 'supplier.address.email' => 'test@example.com',
			'supplier.address.telefax' => '055544332212', 'supplier.address.website' => 'www.example.com',
			'supplier.address.longitude' => '10.0', 'supplier.address.latitude' => '53.5',
			'supplier.address.position' => '0',
		]],
		'lists' => [
			'text' => [[
				'text.languageid' => null, 'text.type' => 'name',
				'text.label' => 'supplier/name', 'text.content' => 'Test supplier',
			], [
				'text.languageid' => null, 'text.type' => 'short',
				'text.label' => 'supplier/short', 'text.content' => 'Short supplier description',
			], [
				'text.languageid' => null, 'text.type' => 'long',
				'text.label' => 'supplier/description', 'text.content' => 'Supplier description',
				'supplier.lists.datestart' => '2010-01-01 00:00:00', 'supplier.lists.dateend' => '2100-01-01 00:00:00',
				'supplier.lists.position' => 1,
			]],
			'media' => [[
				'media.languageid' => null, 'media.type' => 'default',
				'media.label' => 'path/to/supplier.jpg', 'media.url' => 'path/to/supplier.jpg',
				'media.previews' => [1 => 'path/to/supplier.jpg'], 'media.status' => 1, 'media.mimetype' => 'image/jpeg',
			]],
			'product' => [[
				'supplier.lists.datestart' => '2010-01-01 00:00:00', 'supplier.lists.dateend' => '2100-01-01 00:00:00',
				'supplier.lists.position' => 1, 'ref' => 'Cafe Noire Cappuccino',
			], [
				'supplier.lists.datestart' => '2010-01-01 00:00:00',
				'supplier.lists.position' => 2, 'ref' => 'Cafe Noire Expresso',
			]],
		],
	], [
		'supplier.label' => 'Unit Supplier 002', 'supplier.code' => 'unitSupplier002', 'supplier.status' => 1,
		'address' => [[
			'supplier.address.company' => 'Example company LLC', 'supplier.address.vatid' => 'DE999999999',
			'supplier.address.salutation' => 'mrs', 'supplier.address.title' => '',
			'supplier.address.firstname' => 'Good', 'supplier.address.lastname' => 'Unittest',
			'supplier.address.address1' => 'Pickhuben', 'supplier.address.address2' => '2',
			'supplier.address.address3' => '', 'supplier.address.postal' => '20457',
			'supplier.address.city' => 'Hamburg', 'supplier.address.state' => 'Hamburg',
			'supplier.address.countryid' => 'de', 'supplier.address.languageid' => 'de',
			'supplier.address.telephone' => '055544332211', 'supplier.address.email' => 'test@example.com',
			'supplier.address.telefax' => '055544332212', 'supplier.address.website' => 'www.example.com',
			'supplier.address.longitude' => '13.0', 'supplier.address.latitude' => '52.5',
			'supplier.address.position' => '1', 'supplier.address.birthday' => '2001-01-01',
		]],
	], [
		'supplier.label' => 'Unit Supplier 003', 'supplier.code' => 'unitSupplier003', 'supplier.status' => 0,
		'address' => [[
			'supplier.address.company' => 'unitcompany', 'supplier.address.vatid' => 'vatnumber',
			'supplier.address.salutation' => 'company', 'supplier.address.title' => 'unittitle',
			'supplier.address.firstname' => 'unitfirstname', 'supplier.address.lastname' => 'unitlastname',
			'supplier.address.address1' => 'unitaddress1', 'supplier.address.address2' => 'unitaddress2',
			'supplier.address.address3' => 'unitaddress3', 'supplier.address.postal' => 'unitpostal',
			'supplier.address.city' => 'unitcity', 'supplier.address.state' => 'unitstate',
			'supplier.address.countryid' => 'de', 'supplier.address.languageid' => 'de',
			'supplier.address.telephone' => '1234567890', 'supplier.address.email' => 'test2@example.com',
			'supplier.address.telefax' => '1234567891', 'supplier.address.website' => 'unit.web.site',
			'supplier.address.longitude' => '10.0', 'supplier.address.latitude' => '52.5',
			'supplier.address.position' => '2',
		]],
	]],
];
