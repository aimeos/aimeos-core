<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

return [[
	'order.invoiceno' => 'UINV-001', 'order.channel' => 'web', 'order.statuspayment' => 6,
	'order.statusdelivery' => 4, 'order.datepayment' => '2008-02-15 12:34:56',
	'order.sitecode' => 'unittest',
	'order.customerref' => 'ABC-1234', 'order.languageid' => 'de',
	'order.comment' => 'This is a comment if an order. It can be added by the user.',
	'status' => [[
		'order.status.type' => 'typestatus', 'order.status.value' => 'shipped'
	]],
	'address' => [[
		'order.address.type' => 'payment', 'order.address.addressid' => '103',
		'order.address.company' => 'Example company', 'order.address.vatid' => 'DE999999999',
		'order.address.salutation' => 'mr', 'order.address.title' => '',
		'order.address.firstname' => 'Our', 'order.address.lastname' => 'Unittest',
		'order.address.address1' => 'Durchschnitt', 'order.address.address2' => '1',
		'order.address.address3' => '', 'order.address.postal' => '20146',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332211', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332213', 'order.address.website' => 'www.example.net',
		'order.address.longitude' => '11.0', 'order.address.latitude' => '52.0',
		'order.address.birthday' => '2001-01-01',
	], [
		'order.address.type' => 'delivery', 'order.address.addressid' => '101',
		'order.address.company' => 'Example company', 'order.address.vatid' => 'DE999999999',
		'order.address.salutation' => 'mr', 'order.address.title' => 'Dr.',
		'order.address.firstname' => 'Our', 'order.address.lastname' => 'Unittest',
		'order.address.address1' => 'Pickhuben', 'order.address.address2' => '2-4',
		'order.address.address3' => '', 'order.address.postal' => '20457',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332211', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332212', 'order.address.website' => 'www.example.com',
		'order.address.longitude' => '10.0', 'order.address.latitude' => '50.0',
	]],
	'product' => [[
		'price.value' => '4.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'CNE',
		'order.product.vendor' => 'Test vendor', 'order.product.scale' => 0.1,
		'order.product.stocktype' => 'default', 'order.product.name' => 'Cafe Noire Expresso',
		'order.product.mediaurl' => 'somewhere/thump1.jpg', 'order.product.quantity' => 9, 'order.product.qtyopen' => 6,
		'order.product.flags' => '0', 'order.product.position' => 1,
		'order.product.statuspayment' => 5, 'order.product.statusdelivery' => 1,
		'order.product.timeframe' => '4-5d', 'order.product.notes' => 'test note',
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'width',
			'order.product.attribute.value' => 33, 'order.product.attribute.name' => '33',
			'order.product.attribute.quantity' => 1, 'order.product.attribute.price' => '1.00',
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'length',
			'order.product.attribute.value' => 36, 'order.product.attribute.name' => '36',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'config', 'order.product.attribute.code' => 'interval',
			'order.product.attribute.value' => 'P0Y1M0W0D', 'order.product.attribute.name' => 'P0Y1M0W0D',
			'order.product.attribute.quantity' => 1
		]]
	], [
		'price.value' => '6.00', 'price.costs' => '0.50', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'CNC',
		'order.product.vendor' => 'Test vendor', 'order.product.scale' => 0.1,
		'order.product.stocktype' => 'default', 'order.product.name' => 'Cafe Noire Cappuccino',
		'order.product.mediaurl' => 'somewhere/thump2.jpg', 'order.product.quantity' => 3, 'order.product.qtyopen' => 3,
		'order.product.flags' => '0', 'order.product.position' => 2, 'order.product.statusdelivery' => 1,
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'size',
			'order.product.attribute.value' => 's', 'order.product.attribute.name' => 'small',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'color',
			'order.product.attribute.value' => 'blue', 'order.product.attribute.name' => 'blau',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'config', 'order.product.attribute.code' => 'interval',
			'order.product.attribute.value' => 'P1Y0M0W0D', 'order.product.attribute.name' => 'P1Y0M0W0D',
			'order.product.attribute.quantity' => 1
		]]
	], [
		'price.value' => '-5.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'U:MD',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Unittest: Monetary rebate',
		'order.product.mediaurl' => 'somewhere/thump3.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '1', 'order.product.position' => 3, 'order.product.statusdelivery' => 1,
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'size',
			'order.product.attribute.value' => 's', 'order.product.attribute.name' => 'small',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'color',
			'order.product.attribute.value' => 'white', 'order.product.attribute.name' => 'weiss',
			'order.product.attribute.quantity' => 1
		]]
	], [
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '4.50', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'ABCD',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'unitstock', 'order.product.name' => '16 discs',
		'order.product.mediaurl' => 'somewhere/thump4.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '1', 'order.product.position' => 4, 'order.product.statusdelivery' => 1,
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'width',
			'order.product.attribute.value' => 32, 'order.product.attribute.name' => '32',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'length',
			'order.product.attribute.value' => 30, 'order.product.attribute.name' => '30',
			'order.product.attribute.quantity' => 1
		]]
	]],
	'service' => [[
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'payment', 'order.service.code' => 'unitpaymentcode',
		'order.service.name' => 'unitpaymentcode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg',
		'attribute' => [[
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'account owner',
			'order.service.attribute.code' => 'ACOWNER', 'order.service.attribute.value' => 'test user',
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'account number',
			'order.service.attribute.code' => 'ACSTRING', 'order.service.attribute.value' => 9876543,
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'payment method',
			'order.service.attribute.code' => 'NAME', 'order.service.attribute.value' => 'CreditCard',
			'order.service.attribute.quantity' => 1, 'order.service.attribute.price' => '1.00',
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'reference id',
			'order.service.attribute.code' => 'REFID', 'order.service.attribute.value' => 12345678,
			'order.service.attribute.quantity' => 1, 'order.service.attribute.price' => '0.50',
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'transaction date',
			'order.service.attribute.code' => 'TXDATE', 'order.service.attribute.value' => '2009-08-18',
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'transaction account',
			'order.service.attribute.code' => 'X-ACCOUNT', 'order.service.attribute.value' => 'Kraft02',
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'transaction status',
			'order.service.attribute.code' => 'X-STATUS', 'order.service.attribute.value' => 9,
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'unitpaymentcode alias name',
			'order.service.attribute.code' => 'unitpaymentcode-alias-name', 'order.service.attribute.value' => 'aliasName',
			'order.service.attribute.quantity' => 1
		], [
			'order.service.attribute.type' => 'payment', 'order.service.attribute.name' => 'unitpaymentcode alias value',
			'order.service.attribute.code' => 'unitpaymentcode-alias-value', 'order.service.attribute.value' => 'aliasValue',
			'order.service.attribute.quantity' => 1
		]],
		'transaction' => [[
			'order.service.transaction.type' => 'payment',
			'order.service.transaction.currencyid' => 'EUR',
			'order.service.transaction.price' => '18.00',
			'order.service.transaction.status' => 6,
			'order.service.transaction.config' => ['tx' => '1-789'],
		]],
	], [
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'delivery', 'order.service.code' => 'unitdeliverycode',
		'order.service.name' => 'unitdeliverycode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg'
	]],
	'coupon' => [[
		'ordprodpos' => 2, 'code' => '1234'
	], [
		'ordprodpos' => 3, 'code' => 'OPQR'
	]]
], [
	'order.invoiceno' => 'UINV-002', 'order.channel' => 'phone', 'order.statuspayment' => 6,
	'order.statusdelivery' => 4, 'order.datepayment' => '2009-09-17 16:14:32',
	'order.sitecode' => 'unittest',
	'order.languageid' => 'de', 'order.customerref' => 'ABC-9876',
	'order.comment' => 'This is another comment.',
	'status' => [[
		'order.status.type' => 'typestatus', 'order.status.value' => 'waiting'
	]],
	'address' => [[
		'order.address.type' => 'payment', 'order.address.addressid' => '104',
		'order.address.salutation' => 'ms',
		'order.address.firstname' => 'Adelheid', 'order.address.lastname' => 'Mustertest',
		'order.address.address1' => 'Königallee', 'order.address.address2' => '1',
		'order.address.address3' => '', 'order.address.postal' => '20146',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332211', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332213', 'order.address.website' => 'www.example.net',
		'order.address.longitude' => '10.0', 'order.address.latitude' => '50.0'
	], [
		'order.address.type' => 'delivery', 'order.address.addressid' => '102',
		'order.address.company' => 'Example company', 'order.address.vatid' => 'DE999999999',
		'order.address.salutation' => 'ms', 'order.address.title' => 'Dr.',
		'order.address.firstname' => 'Maria', 'order.address.lastname' => 'Mustertest',
		'order.address.address1' => 'Pickhuben', 'order.address.address2' => '2',
		'order.address.address3' => '', 'order.address.postal' => '20457',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332211', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332212', 'order.address.website' => 'www.example.com',
		'order.address.longitude' => '10.5', 'order.address.latitude' => '51.0'
	]],
	'product' => [[
		'price.value' => '36.00', 'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '19.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'CNE',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'default', 'order.product.name' => 'Cafe Noire Expresso',
		'order.product.mediaurl' => 'somewhere/thump5.jpg', 'order.product.quantity' => 2, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 1, 'order.product.statusdelivery' => 1
	], [
		'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '19.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'CNC',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'default', 'order.product.name' => 'Cafe Noire Cappuccino',
		'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 2, 'order.product.statusdelivery' => 1
	]],
	'service' => [[
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'payment', 'order.service.code' => 'unitpaymentcode',
		'order.service.name' => 'unitpaymentcode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg',
		'transaction' => [[
			'order.service.transaction.type' => 'payment',
			'order.service.transaction.currencyid' => 'EUR',
			'order.service.transaction.price' => '672.00',
			'order.service.transaction.status' => 6,
			'order.service.transaction.config' => ['tx' => '2-456'],
		]],
	], [
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '5.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'delivery', 'order.service.code' => 'unitdeliverycode',
		'order.service.name' => 'unitdeliverycode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg'
	]],
	'coupon' => [[
		'ordprodpos' => 0, 'code' => '5678'
	], [
		'ordprodpos' => 1, 'code' => 'OPQR'
	]]
], [
	'order.invoiceno' => 'UINV-003', 'order.channel' => 'web', 'order.statuspayment' => 6,
	'order.statusdelivery' => 4, 'order.datepayment' => '2009-03-18 16:14:32',
	'order.sitecode' => 'unittest',
	'order.languageid' => 'de', 'order.customerref' => 'XYZ-1234',
	'order.comment' => 'This is a bundle basket.',
	'address' => [[
		'order.address.type' => 'payment', 'order.address.addressid' => '107',
		'order.address.salutation' => 'ms', 'order.address.title' => '',
		'order.address.firstname' => 'Adelheid', 'order.address.lastname' => 'Mustertest',
		'order.address.address1' => 'Königallee', 'order.address.address2' => '1',
		'order.address.address3' => '', 'order.address.postal' => '20146',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332211', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332213', 'order.address.website' => 'www.example.net',
		'order.address.longitude' => '10.0', 'order.address.latitude' => '50.0',
	]],
	'product' => [[
		'price.value' => '1200.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
		'order.product.type' => 'bundle', 'order.product.prodcode' => 'bdl:zyx',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest1',
		'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 1, 'order.product.statusdelivery' => 1,
		'product' => [[
			'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '16.00'],
			'order.product.type' => 'default', 'order.product.prodcode' => 'bdl:EFG',
			'order.product.vendor' => 'Test vendor',
			'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest1',
			'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
			'order.product.flags' => '0', 'order.product.position' => 2, 'order.product.statusdelivery' => 1
		], [
			'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
			'order.product.type' => 'default', 'order.product.prodcode' => 'bdl:HIJ',
			'order.product.vendor' => 'Test vendor',
			'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest 1',
			'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
			'order.product.flags' => '0', 'order.product.position' => 3, 'order.product.statusdelivery' => 1
		]],
	], [
		'price.value' => '1200.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '17.00'],
		'order.product.type' => 'bundle', 'order.product.prodcode' => 'bdl:hal',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest2',
		'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 4, 'order.product.statusdelivery' => 1,
		'product' => [[
			'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '16.00'],
			'order.product.type' => 'default', 'order.product.prodcode' => 'bdl:EFX',
			'order.product.vendor' => 'Test vendor',
			'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest 2',
			'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
			'order.product.flags' => '0', 'order.product.position' => 5, 'order.product.statusdelivery' => 1
		], [
			'price.value' => '600.00', 'price.costs' => '30.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '18.00'],
			'order.product.type' => 'default', 'order.product.prodcode' => 'bdl:HKL',
			'order.product.vendor' => 'Test vendor',
			'order.product.stocktype' => 'unitstock', 'order.product.name' => 'Bundle Unittest 2',
			'order.product.mediaurl' => 'somewhere/thump6.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
			'order.product.flags' => '0', 'order.product.position' => 6, 'order.product.statusdelivery' => 1
		]]
	]],
	'service' => [[
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'payment', 'order.service.code' => 'directdebit-test',
		'order.service.name' => 'DirectDebit', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg',
		'transaction' => [[
			'order.service.transaction.type' => 'payment',
			'order.service.transaction.currencyid' => 'EUR',
			'order.service.transaction.price' => '2465.00',
			'order.service.transaction.status' => 6,
			'order.service.transaction.config' => ['tx' => '3-123'],
		]],
	], [
		'price.value' => '0.00', 'price.costs' => '5.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'delivery', 'order.service.code' => 'unitdeliverycode',
		'order.service.name' => 'unitdeliverycode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg'
	]]
], [
	'order.invoiceno' => 'UINV-004', 'order.channel' => 'web', 'order.statuspayment' => 5,
	'order.statusdelivery' => 3, 'order.datepayment' => '2011-09-17 16:14:32',
	'order.sitecode' => 'unittest',
	'order.languageid' => 'de', 'order.customerref' => 'XYZ-9876',
	'order.comment' => 'This is a comment if an order. It can be added by the user.',
	'status' => [[
		'order.status.type' => 'status', 'order.status.value' => 'waiting'
	]],
	'address' => [[
		'order.address.type' => 'payment', 'order.address.addressid' => '106',
		'order.address.salutation' => 'mr', 'order.address.title' => '',
		'order.address.firstname' => 'Our', 'order.address.lastname' => 'Unittest',
		'order.address.address1' => 'Durchschnitt', 'order.address.address2' => '2',
		'order.address.address3' => '', 'order.address.postal' => '20146',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332212', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332213', 'order.address.website' => 'www.example.net',
		'order.address.longitude' => '11.0', 'order.address.latitude' => '52.0'
	], [
		'order.address.type' => 'delivery', 'order.address.addressid' => '105',
		'order.address.company' => 'Example company', 'order.address.vatid' => 'DE999999999',
		'order.address.salutation' => 'ms', 'order.address.title' => 'Dr.',
		'order.address.firstname' => 'Our', 'order.address.lastname' => 'Unittest',
		'order.address.address1' => 'Pickhuben', 'order.address.address2' => '2-4',
		'order.address.address3' => '', 'order.address.postal' => '20457',
		'order.address.city' => 'Hamburg', 'order.address.state' => 'Hamburg',
		'order.address.countryid' => 'de', 'order.languageid' => 'de',
		'order.address.telephone' => '055544332212', 'order.address.email' => 'test@example.com',
		'order.address.telefax' => '055544332212', 'order.address.website' => 'www.example.com',
		'order.address.longitude' => '10.5', 'order.address.latitude' => '51.0'
	]],
	'product' => [[
		'price.value' => '4.50', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'CNE',
		'order.product.vendor' => 'Test vendor', 'order.product.scale' => 0.1,
		'order.product.stocktype' => 'default', 'order.product.name' => 'Cafe Noire Expresso',
		'order.product.mediaurl' => 'somewhere/thump1.jpg', 'order.product.quantity' => 3, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 1, 'order.product.statusdelivery' => 1,
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'width',
			'order.product.attribute.value' => 32, 'order.product.attribute.name' => '32',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'length',
			'order.product.attribute.value' => 36, 'order.product.attribute.name' => '36',
			'order.product.attribute.quantity' => 1
		]]
	], [
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '4.50', 'price.taxrates' => ['' => '0.00'],
		'order.product.type' => 'default', 'order.product.prodcode' => 'ABCD',
		'order.product.vendor' => 'Test vendor',
		'order.product.stocktype' => 'unitstock', 'order.product.name' => '16 discs',
		'order.product.mediaurl' => 'somewhere/thump4.jpg', 'order.product.quantity' => 1, 'order.product.qtyopen' => 0,
		'order.product.flags' => '0', 'order.product.position' => 2, 'order.product.statusdelivery' => 1,
		'attribute' => [[
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'width',
			'order.product.attribute.value' => 32, 'order.product.attribute.name' => '32',
			'order.product.attribute.quantity' => 1
		], [
			'order.product.attribute.type' => 'default', 'order.product.attribute.code' => 'length',
			'order.product.attribute.value' => 30, 'order.product.attribute.name' => '30',
			'order.product.attribute.quantity' => 1
		]]
	]],
	'service' => [[
		'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'payment', 'order.service.code' => 'paypalexpress',
		'order.service.name' => 'paypal', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg'
	], [
		'price.value' => '0.00', 'price.costs' => '5.00', 'price.rebate' => '0.00', 'price.taxrates' => ['' => '0.00'],
		'order.service.type' => 'delivery', 'order.service.code' => 'unitdeliverycode',
		'order.service.name' => 'unitdeliverycode', 'order.service.currencyid' => 'EUR',
		'order.service.mediaurl' => 'somewhere/thump1.jpg'
	]]
]];
