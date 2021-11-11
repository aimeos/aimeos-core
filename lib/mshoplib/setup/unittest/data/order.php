<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [[
	'order.type' => 'web', 'order.statuspayment' => 6, 'order.statusdelivery' => 4, 'order.datepayment' => '2008-02-15 12:34:56',
	'status' => [[
		'order.status.type' => 'typestatus', 'order.status.value' => 'shipped'
	]],
	'base' => [
		'order.base.sitecode' => 'unittest',
		'order.base.customerref' => 'ABC-1234', 'order.base.languageid' => 'de',
		'order.base.comment' => 'This is a comment if an order. It can be added by the user.',
		'address' => [[
			'order.base.address.type' => 'payment', 'order.base.address.addressid' => '103',
			'order.base.address.company' => 'Example company', 'order.base.address.vatid' => 'DE999999999',
			'order.base.address.salutation' => 'mr', 'order.base.address.title' => '',
			'order.base.address.firstname' => 'Our', 'order.base.address.lastname' => 'Unittest',
			'order.base.address.address1' => 'Durchschnitt', 'order.base.address.address2' => '1',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20146',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332211', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332213', 'order.base.address.website' => 'www.example.net',
			'order.base.address.longitude' => '11.0', 'order.base.address.latitude' => '52.0',
			'order.base.address.birthday' => '2001-01-01',
		], [
			'order.base.address.type' => 'delivery', 'order.base.address.addressid' => '101',
			'order.base.address.company' => 'Example company', 'order.base.address.vatid' => 'DE999999999',
			'order.base.address.salutation' => 'mr', 'order.base.address.title' => 'Dr.',
			'order.base.address.firstname' => 'Our', 'order.base.address.lastname' => 'Unittest',
			'order.base.address.address1' => 'Pickhuben', 'order.base.address.address2' => '2-4',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20457',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332211', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332212', 'order.base.address.website' => 'www.example.com',
			'order.base.address.longitude' => '10.0', 'order.base.address.latitude' => '50.0',
		]],
		'product' => [[
			'price.value' => '4.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'CNE',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'default', 'order.base.product.name' => 'Cafe Noire Expresso',
			'order.base.product.mediaurl' => 'somewhere/thump1.jpg', 'order.base.product.quantity' => 9, 'order.base.product.qtyopen' => 6,
			'order.base.product.flags' => '0', 'order.base.product.position' => 1,
			'order.base.product.statuspayment' => 5, 'order.base.product.statusdelivery' => 1,
			'order.base.product.timeframe' => '4-5d', 'order.base.product.notes' => 'test note',
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'width',
				'order.base.product.attribute.value' => 33, 'order.base.product.attribute.name' => '33',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'length',
				'order.base.product.attribute.value' => 36, 'order.base.product.attribute.name' => '36',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'config', 'order.base.product.attribute.code' => 'interval',
				'order.base.product.attribute.value' => 'P0Y1M0W0D', 'order.base.product.attribute.name' => 'P0Y1M0W0D',
				'order.base.product.attribute.quantity' => 1
			]]
		], [
			'price.value' => '6.00', 'price.costs' => '0.50', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'CNC',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'default', 'order.base.product.name' => 'Cafe Noire Cappuccino',
			'order.base.product.mediaurl' => 'somewhere/thump2.jpg', 'order.base.product.quantity' => 3, 'order.base.product.qtyopen' => 3,
			'order.base.product.flags' => '0', 'order.base.product.position' => 2, 'order.base.product.statusdelivery' => 1,
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'size',
				'order.base.product.attribute.value' => 's', 'order.base.product.attribute.name' => 'small',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'color',
				'order.base.product.attribute.value' => 'blue', 'order.base.product.attribute.name' => 'blau',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'config', 'order.base.product.attribute.code' => 'interval',
				'order.base.product.attribute.value' => 'P1Y0M0W0D', 'order.base.product.attribute.name' => 'P1Y0M0W0D',
				'order.base.product.attribute.quantity' => 1
			]]
		], [
			'price.value' => '-5.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'U:MD',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Unittest: Monetary rebate',
			'order.base.product.mediaurl' => 'somewhere/thump3.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '1', 'order.base.product.position' => 3, 'order.base.product.statusdelivery' => 1,
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'size',
				'order.base.product.attribute.value' => 's', 'order.base.product.attribute.name' => 'small',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'color',
				'order.base.product.attribute.value' => 'white', 'order.base.product.attribute.name' => 'weiss',
				'order.base.product.attribute.quantity' => 1
			]]
		], [
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '4.50', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'ABCD',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => '16 discs',
			'order.base.product.mediaurl' => 'somewhere/thump4.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '1', 'order.base.product.position' => 4, 'order.base.product.statusdelivery' => 1,
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'width',
				'order.base.product.attribute.value' => 32, 'order.base.product.attribute.name' => '32',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'length',
				'order.base.product.attribute.value' => 30, 'order.base.product.attribute.name' => '30',
				'order.base.product.attribute.quantity' => 1
			]]
		]],
		'service' => [[
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'payment', 'order.base.service.code' => 'unitpaymentcode',
			'order.base.service.name' => 'unitpaymentcode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg',
			'attribute' => [[
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'account owner',
				'order.base.service.attribute.code' => 'ACOWNER', 'order.base.service.attribute.value' => 'test user',
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'account number',
				'order.base.service.attribute.code' => 'ACSTRING', 'order.base.service.attribute.value' => 9876543,
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'payment method',
				'order.base.service.attribute.code' => 'NAME', 'order.base.service.attribute.value' => 'CreditCard',
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'reference id',
				'order.base.service.attribute.code' => 'REFID', 'order.base.service.attribute.value' => 12345678,
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'transaction date',
				'order.base.service.attribute.code' => 'TXDATE', 'order.base.service.attribute.value' => '2009-08-18',
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'transaction account',
				'order.base.service.attribute.code' => 'X-ACCOUNT', 'order.base.service.attribute.value' => 'Kraft02',
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'transaction status',
				'order.base.service.attribute.code' => 'X-STATUS', 'order.base.service.attribute.value' => 9,
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'unitpaymentcode alias name',
				'order.base.service.attribute.code' => 'unitpaymentcode-alias-name', 'order.base.service.attribute.value' => 'aliasName',
				'order.base.service.attribute.quantity' => 1
			], [
				'order.base.service.attribute.type' => 'payment', 'order.base.service.attribute.name' => 'unitpaymentcode alias value',
				'order.base.service.attribute.code' => 'unitpaymentcode-alias-value', 'order.base.service.attribute.value' => 'aliasValue',
				'order.base.service.attribute.quantity' => 1
			]]
		], [
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'delivery', 'order.base.service.code' => 'unitdeliverycode',
			'order.base.service.name' => 'unitdeliverycode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		]],
		'coupon' => [[
			'ordprodpos' => 2, 'code' => '5678'
		], [
			'ordprodpos' => 3, 'code' => 'OPQR'
		]]
	]
], [
	'order.type' => 'phone', 'order.statuspayment' => 6, 'order.statusdelivery' => 4, 'order.datepayment' => '2009-09-17 16:14:32',
	'status' => [[
		'order.status.type' => 'typestatus', 'order.status.value' => 'waiting'
	]],
	'base' => [
		'order.base.sitecode' => 'unittest',
		'order.base.languageid' => 'de', 'order.base.customerref' => 'ABC-9876',
		'order.base.comment' => 'This is another comment.',
		'address' => [[
			'order.base.address.type' => 'payment', 'order.base.address.addressid' => '104',
			'order.base.address.salutation' => 'mrs',
			'order.base.address.firstname' => 'Adelheid', 'order.base.address.lastname' => 'Mustertest',
			'order.base.address.address1' => 'Königallee', 'order.base.address.address2' => '1',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20146',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332211', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332213', 'order.base.address.website' => 'www.example.net',
			'order.base.address.longitude' => '10.0', 'order.base.address.latitude' => '50.0'
		], [
			'order.base.address.type' => 'delivery', 'order.base.address.addressid' => '102',
			'order.base.address.company' => 'Example company', 'order.base.address.vatid' => 'DE999999999',
			'order.base.address.salutation' => 'mrs', 'order.base.address.title' => 'Dr.',
			'order.base.address.firstname' => 'Maria', 'order.base.address.lastname' => 'Mustertest',
			'order.base.address.address1' => 'Pickhuben', 'order.base.address.address2' => '2',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20457',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332211', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332212', 'order.base.address.website' => 'www.example.com',
			'order.base.address.longitude' => '10.5', 'order.base.address.latitude' => '51.0'
		]],
		'product' => [[
			'price.value' => '36.00', 'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '19.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'CNE',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'default', 'order.base.product.name' => 'Cafe Noire Expresso',
			'order.base.product.mediaurl' => 'somewhere/thump5.jpg', 'order.base.product.quantity' => 2, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 1, 'order.base.product.statusdelivery' => 1
		], [
			'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '19.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'CNC',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'default', 'order.base.product.name' => 'Cafe Noire Cappuccino',
			'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 2, 'order.base.product.statusdelivery' => 1
		]],
		'service' => [[
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'payment', 'order.base.service.code' => 'unitpaymentcode',
			'order.base.service.name' => 'unitpaymentcode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		], [
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'delivery', 'order.base.service.code' => 'unitdeliverycode',
			'order.base.service.name' => 'unitdeliverycode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		]],
		'coupon' => [[
			'ordprodpos' => 0, 'code' => '5678'
		], [
			'ordprodpos' => 1, 'code' => 'OPQR'
		]]
	],
], [
	'order.type' => 'web', 'order.statuspayment' => 6, 'order.statusdelivery' => 4, 'order.datepayment' => '2009-03-18 16:14:32',
	'base' => [
		'order.base.sitecode' => 'unittest',
		'order.base.languageid' => 'de', 'order.base.customerref' => 'XYZ-1234',
		'order.base.comment' => 'This is a bundle basket.',
		'address' => [[
			'order.base.address.type' => 'payment', 'order.base.address.addressid' => '107',
			'order.base.address.salutation' => 'mrs', 'order.base.address.title' => '',
			'order.base.address.firstname' => 'Adelheid', 'order.base.address.lastname' => 'Mustertest',
			'order.base.address.address1' => 'Königallee', 'order.base.address.address2' => '1',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20146',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332211', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332213', 'order.base.address.website' => 'www.example.net',
			'order.base.address.longitude' => '10.0', 'order.base.address.latitude' => '50.0',
		]],
		'product' => [[
			'price.value' => '1200.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
			'order.base.product.type' => 'bundle', 'order.base.product.prodcode' => 'bdl:zyx',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest1',
			'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 1, 'order.base.product.statusdelivery' => 1,
			'product' => [[
				'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '16.00'],
				'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'bdl:EFG',
				'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
				'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest1',
				'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
				'order.base.product.flags' => '0', 'order.base.product.position' => 2, 'order.base.product.statusdelivery' => 1
			], [
				'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
				'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'bdl:HIJ',
				'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
				'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest 1',
				'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
				'order.base.product.flags' => '0', 'order.base.product.position' => 3, 'order.base.product.statusdelivery' => 1
			]],
		], [
			'price.value' => '1200.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
			'order.base.product.type' => 'bundle', 'order.base.product.prodcode' => 'bdl:hal',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest2',
			'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 4, 'order.base.product.statusdelivery' => 1,
			'product' => [[
				'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '16.00'],
				'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'bdl:EFX',
				'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
				'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest 2',
				'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
				'order.base.product.flags' => '0', 'order.base.product.position' => 5, 'order.base.product.statusdelivery' => 1
			], [
				'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '18.00'],
				'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'bdl:HKL',
				'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
				'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => 'Bundle Unittest 2',
				'order.base.product.mediaurl' => 'somewhere/thump6.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
				'order.base.product.flags' => '0', 'order.base.product.position' => 6, 'order.base.product.statusdelivery' => 1
			]]
		]],
		'service' => [[
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'payment', 'order.base.service.code' => 'directdebit-test',
			'order.base.service.name' => 'DirectDebit', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		], [
			'price.value' => '0.00', 'price.costs' => '5.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'delivery', 'order.base.service.code' => 'unitdeliverycode',
			'order.base.service.name' => 'unitdeliverycode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		]]
	]
], [
	'order.type' => 'web', 'order.statuspayment' => 5, 'order.statusdelivery' => 3, 'order.datepayment' => '2011-09-17 16:14:32',
	'status' => [[
		'order.status.type' => 'status', 'order.status.value' => 'waiting'
	]],
	'base' => [
		'order.base.sitecode' => 'unittest',
		'order.base.languageid' => 'de', 'order.base.customerref' => 'XYZ-9876',
		'order.base.comment' => 'This is a comment if an order. It can be added by the user.',
		'address' => [[
			'order.base.address.type' => 'payment', 'order.base.address.addressid' => '106',
			'order.base.address.salutation' => 'mr', 'order.base.address.title' => '',
			'order.base.address.firstname' => 'Our', 'order.base.address.lastname' => 'Unittest',
			'order.base.address.address1' => 'Durchschnitt', 'order.base.address.address2' => '2',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20146',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332212', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332213', 'order.base.address.website' => 'www.example.net',
			'order.base.address.longitude' => '11.0', 'order.base.address.latitude' => '52.0'
		], [
			'order.base.address.type' => 'delivery', 'order.base.address.addressid' => '105',
			'order.base.address.company' => 'Example company', 'order.base.address.vatid' => 'DE999999999',
			'order.base.address.salutation' => 'mrs', 'order.base.address.title' => 'Dr.',
			'order.base.address.firstname' => 'Our', 'order.base.address.lastname' => 'Unittest',
			'order.base.address.address1' => 'Pickhuben', 'order.base.address.address2' => '2-4',
			'order.base.address.address3' => '', 'order.base.address.postal' => '20457',
			'order.base.address.city' => 'Hamburg', 'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de', 'order.base.languageid' => 'de',
			'order.base.address.telephone' => '055544332212', 'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '055544332212', 'order.base.address.website' => 'www.example.com',
			'order.base.address.longitude' => '10.5', 'order.base.address.latitude' => '51.0'
		]],
		'product' => [[
			'price.value' => '4.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'CNE',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'default', 'order.base.product.name' => 'Cafe Noire Expresso',
			'order.base.product.mediaurl' => 'somewhere/thump1.jpg', 'order.base.product.quantity' => 3, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 1, 'order.base.product.statusdelivery' => 1,
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'width',
				'order.base.product.attribute.value' => 32, 'order.base.product.attribute.name' => '32',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'length',
				'order.base.product.attribute.value' => 36, 'order.base.product.attribute.name' => '36',
				'order.base.product.attribute.quantity' => 1
			]]
		], [
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '4.50', 'price.taxrates' => ['' => '0.00'],
			'order.base.product.type' => 'default', 'order.base.product.prodcode' => 'ABCD',
			'order.base.product.suppliername' => 'Test supplier', 'order.base.product.supplierid' => 'unitSupplier001',
			'order.base.product.stocktype' => 'unitstock', 'order.base.product.name' => '16 discs',
			'order.base.product.mediaurl' => 'somewhere/thump4.jpg', 'order.base.product.quantity' => 1, 'order.base.product.qtyopen' => 0,
			'order.base.product.flags' => '0', 'order.base.product.position' => 2, 'order.base.product.statusdelivery' => 1,
			'attribute' => [[
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'width',
				'order.base.product.attribute.value' => 32, 'order.base.product.attribute.name' => '32',
				'order.base.product.attribute.quantity' => 1
			], [
				'order.base.product.attribute.type' => 'default', 'order.base.product.attribute.code' => 'length',
				'order.base.product.attribute.value' => 30, 'order.base.product.attribute.name' => '30',
				'order.base.product.attribute.quantity' => 1
			]]
		]],
		'service' => [[
			'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'payment', 'order.base.service.code' => 'paypalexpress',
			'order.base.service.name' => 'paypal', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		], [
			'price.value' => '0.00', 'price.costs' => '5.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
			'order.base.service.type' => 'delivery', 'order.base.service.code' => 'unitdeliverycode',
			'order.base.service.name' => 'unitdeliverycode', 'order.base.service.currencyid' => 'EUR',
			'order.base.service.mediaurl' => 'somewhere/thump1.jpg'
		]]
	]
]];
