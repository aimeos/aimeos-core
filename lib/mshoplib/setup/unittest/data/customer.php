<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'customer/lists/type' => [
		['customer.lists.type.domain' => 'customer/group', 'customer.lists.type.code' => 'default', 'customer.lists.type.label' => 'Standard', 'customer.lists.type.status' => 1],
		['customer.lists.type.domain' => 'order', 'customer.lists.type.code' => 'download', 'customer.lists.type.label' => 'Download', 'customer.lists.type.status' => 1],
		['customer.lists.type.domain' => 'product', 'customer.lists.type.code' => 'favorite', 'customer.lists.type.label' => 'Favorite', 'customer.lists.type.status' => 1],
		['customer.lists.type.domain' => 'product', 'customer.lists.type.code' => 'watch', 'customer.lists.type.label' => 'Watch list', 'customer.lists.type.status' => 1],
		['customer.lists.type.domain' => 'service', 'customer.lists.type.code' => 'default', 'customer.lists.type.label' => 'Standard', 'customer.lists.type.status' => 1],
		['customer.lists.type.domain' => 'text', 'customer.lists.type.code' => 'default', 'customer.lists.type.label' => 'Standard', 'customer.lists.type.status' => 1],
	],

	'customer/property/type' => [
		['customer.property.type.domain' => 'customer', 'customer.property.type.code' => 'newsletter', 'customer.property.type.label' => 'Newsletter', 'customer.property.type.status' => 1],
	],

	'customer/group' => [
		['customer.group.code' => 'unitgroup', 'customer.group.label' => 'Unitgroup'],
		['customer.group.code' => 'unitgroup2', 'customer.group.label' => 'Unitgroup 2'],
	],

	'customer' => [
		'customer/test@example.com' => [
			'customer.label' => 'unitCustomer001', 'customer.code' => 'test@example.com', 'customer.status' => 1,
			'customer.company' => 'Example company', 'customer.vatid' => 'DE999999999', 'customer.salutation' => 'mr',
			'customer.title' => 'Dr', 'customer.firstname' => 'Our', 'customer.lastname' => 'Unittest',
			'customer.address1' => 'Pickhuben', 'customer.address2' => '2-4', 'customer.address3' => '',
			'customer.postal' => '20457', 'customer.city' => 'Hamburg', 'customer.state' => 'Hamburg',
			'customer.countryid' => 'DE', 'customer.languageid' => 'de', 'customer.telephone' => '055544332211',
			'customer.email' => 'test@example.com', 'customer.telefax' => '055544332212', 'customer.website' => 'www.example.com',
			'customer.longitude' => '10.0', 'customer.latitude' => '50.0', 'customer.birthday' => '1999-01-01',
			'customer.password' => 'unittest',
			'address' => [[
				'customer.address.company' => 'Example company', 'customer.address.vatid' => 'DE999999999',
				'customer.address.salutation' => 'mr', 'customer.address.title' => 'Dr',
				'customer.address.firstname' => 'Our', 'customer.address.lastname' => 'Unittest',
				'customer.address.address1' => 'Pickhuben', 'customer.address.address2' => '2-4',
				'customer.address.address3' => '', 'customer.address.postal' => '20457',
				'customer.address.city' => 'Hamburg', 'customer.address.state' => 'Hamburg',
				'customer.address.countryid' => 'DE', 'customer.address.languageid' => 'de',
				'customer.address.telephone' => '055544332211', 'customer.address.email' => 'test@example.com',
				'customer.address.telefax' => '055544332212', 'customer.address.website' => 'www.example.com',
				'customer.address.longitude' => '10.0', 'customer.address.latitude' => '50.0',
				'customer.address.position' => '0', 'customer.address.birthday' => '2000-01-01',
			]],
			'property' => [[
				'customer.property.type' => 'newsletter', 'customer.property.languageid' => null, 'customer.property.value' => '1'
			]],
			'group' => ['unitgroup'],
			'lists' => [
				'text' => [[
					'customer.lists.type' => 'default', 'customer.lists.position' => 0,
					'customer.lists.datestart' => '2010-01-01 00:00:00', 'customer.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'information', 'text.domain' => 'customer', 'text.status' => 1,
					'text.label' => 'customer/information', 'text.content' => 'Customer information',
				]],
				'product' => [[
					'customer.lists.type' => 'watch', 'customer.lists.position' => 1, 'customer.lists.config' => ['stock' => 1],
					'customer.lists.datestart' => null, 'customer.lists.dateend' => '2100-01-01 00:00:00',
					'ref' => 'Cafe Noire Expresso',
				], [
					'customer.lists.type' => 'favorite', 'customer.lists.position' => 1, 'customer.lists.config' => [],
					'customer.lists.datestart' => null, 'customer.lists.dateend' => '2100-01-01 00:00:00',
					'ref' => 'Cafe Noire Expresso',
				]],
			],
		],
		'customer/test2@example.com' => [
			'customer.label' => 'unitCustomer002', 'customer.code' => 'test2@example.com', 'customer.status' => 1,
			'customer.countryid' => 'DE', 'customer.languageid' => 'de', 'customer.email' => 'test2@example.com',
			'address' => [[
				'customer.address.company' => 'Example company LLC', 'customer.address.vatid' => 'DE999999999',
				'customer.address.salutation' => 'mr', 'customer.address.title' => 'Dr.',
				'customer.address.firstname' => 'Good', 'customer.address.lastname' => 'Unittest',
				'customer.address.address1' => 'Pickhuben', 'customer.address.address2' => '2-4',
				'customer.address.address3' => '', 'customer.address.postal' => '20457',
				'customer.address.city' => 'Hamburg', 'customer.address.state' => 'Hamburg',
				'customer.address.countryid' => 'DE', 'customer.address.languageid' => 'de',
				'customer.address.telephone' => '055544332211', 'customer.address.email' => 'test@example.com',
				'customer.address.telefax' => '055544332212', 'customer.address.website' => 'www.example.com',
				'customer.address.longitude' => '10.5', 'customer.address.latitude' => '51.0',
				'customer.address.position' => '0', 'customer.birthday' => '2000-01-01',
			], [
				'customer.address.company' => 'Example company LLC', 'customer.address.vatid' => 'DE999999999',
				'customer.address.salutation' => 'mr', 'customer.address.title' => 'Dr.',
				'customer.address.firstname' => 'Good', 'customer.address.lastname' => 'Unittest',
				'customer.address.address1' => 'Pickhuben', 'customer.address.address2' => '2-4',
				'customer.address.address3' => '', 'customer.address.postal' => '11099',
				'customer.address.city' => 'Berlin', 'customer.address.state' => 'Berlin',
				'customer.address.countryid' => 'DE', 'customer.address.languageid' => 'de',
				'customer.address.telephone' => '055544332221', 'customer.address.email' => 'test@example.com',
				'customer.address.telefax' => '055544333212', 'customer.address.website' => 'www.example.com',
				'customer.address.longitude' => '11.0', 'customer.address.latitude' => '52.0',
				'customer.address.position' => '1',
			]],
		],
		'customer/test3@example.com' => [
			'customer.label' => 'unitCustomer003', 'customer.code' => 'test3@example.com', 'customer.status' => 0,
			'customer.countryid' => 'DE', 'customer.languageid' => 'de', 'customer.email' => 'test3@example.com',
			'address' => [[
				'customer.address.company' => 'unitcompany', 'customer.address.vatid' => 'vatnumber',
				'customer.address.salutation' => 'company', 'customer.address.title' => 'unittitle',
				'customer.address.firstname' => 'unitfirstname', 'customer.address.lastname' => 'unitlastname',
				'customer.address.address1' => 'unitaddress1', 'customer.address.address2' => 'unitaddress2',
				'customer.address.address3' => 'unitaddress3', 'customer.address.postal' => 'unitpostal',
				'customer.address.city' => 'unitcity', 'customer.address.state' => 'unitstate',
				'customer.address.countryid' => 'DE', 'customer.address.languageid' => 'de',
				'customer.address.telephone' => '1234567890', 'customer.address.email' => 'test3@example.com',
				'customer.address.telefax' => '1234567891', 'customer.address.website' => 'unit.web.site',
				'customer.address.longitude' => '10.0', 'customer.address.latitude' => '53.5',
				'customer.address.position' => '2'
			]],
			'lists' => [
				'text' => [[
					'customer.lists.type' => 'default', 'customer.lists.position' => 1,
					'customer.lists.datestart' => '2010-01-01 00:00:00', 'customer.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'information', 'text.domain' => 'customer', 'text.status' => 1,
					'text.label' => 'customer/information', 'text.content' => 'Customer information',
				], [
					'customer.lists.type' => 'default', 'customer.lists.position' => 2,
					'customer.lists.datestart' => '2010-01-01 00:00:00', 'customer.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'notify', 'text.domain' => 'customer', 'text.status' => 1,
					'text.label' => 'customer/notify', 'text.content' => 'Customer notify',
				], [
					'customer.lists.type' => 'default', 'customer.lists.position' => 3,
					'customer.lists.datestart' => '2010-01-01 00:00:00', 'customer.lists.dateend' => '2098-01-01 00:00:00',
					'text.languageid' => null, 'text.type' => 'newsletter', 'text.status' => 1,
					'text.label' => 'customer/newsletter', 'text.content' => 'Customer newsletter',
				]],
			]
		],
	],
];
