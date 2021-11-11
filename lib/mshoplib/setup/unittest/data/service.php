<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'service/type' => [
		['service.type.domain' => 'service', 'service.type.code' => 'payment', 'service.type.label' => 'Payment', 'service.type.status' => 1],
		['service.type.domain' => 'service', 'service.type.code' => 'delivery', 'service.type.label' => 'Delivery', 'service.type.status' => 1],
	],

	'service/lists/type' => [
		['service.lists.type.domain' => 'product', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'attribute', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'catalog', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'media', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'price', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'service', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'text', 'service.lists.type.code' => 'default', 'service.lists.type.label' => 'Standard', 'service.lists.type.status' => 1],
		['service.lists.type.domain' => 'text', 'service.lists.type.code' => 'unittype1', 'service.lists.type.label' => 'Unit type 1', 'service.lists.type.status' => 1],
	],

	'service' => [
		'service/delivery/unitdeliverycode' => [
			'service.type' => 'delivery', 'service.code' => 'unitdeliverycode', 'service.label' => 'unitlabel',
			'service.provider' => 'Standard', 'service.position' => 0, 'service.status' => 1,
			'service.config' => [],
			'lists' => [
				'price' => [[
					'service.lists.type' => 'default', 'service.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'service',
					'price.label'=>'service/default/12.95/1.99', 'price.quantity' => 1, 'price.value' => '12.95',
					'price.costs' => '1.99', 'price.rebate' => '1.05', 'price.taxrate' => '19.00'
				], [
					'service.lists.type' => 'default', 'service.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'service',
					'price.label'=>'service/default/2.95/0.00', 'price.quantity' => 2, 'price.value' => '2.95',
					'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00'
				]],
				'media' => [[
					'service.lists.type' => 'default', 'service.lists.position' => 0,
					'media.languageid' => null, 'media.type' => 'default', 'media.domain' => 'service',
					'media.label' => 'service_image1', 'media.status' => 1, 'media.mimetype' => 'image/png',
					'media.url' => 'path/to/service.png', 'media.previews' => [1 => 'path/to/service.png'],
				]],
				'text' => [[
					'service.lists.type' => 'unittype1', 'service.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text1', 'text.content' => 'Unittest: Service text 1 de', 'text.status' => 1
				], [
					'service.lists.type' => 'unittype1', 'service.lists.position' => 1,
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text2', 'text.content' => 'Unittest: Service text 2 de', 'text.status' => 1
				], [
					'service.lists.type' => 'unittype1', 'service.lists.position' => 2,
					'service.lists.datestart' => '2008-02-17 12:34:58', 'service.lists.dateend' => '2010-01-01 23:59:59',
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text3', 'text.content' => 'Unittest: Service text 3 de', 'text.status' => 0
				], [
					'service.lists.type' => 'default', 'service.lists.position' => 1,
					'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'service',
					'text.label' => 'service_text4', 'text.content' => 'Unittest service name', 'text.status' => 1
				], [
					'service.lists.type' => 'default', 'service.lists.position' => 2,
					'text.languageid' => 'de', 'text.type' => 'short', 'text.domain' => 'service',
					'text.label' => 'service_text5', 'text.content' => 'Short service description', 'text.status' => 1
				], [
					'service.lists.type' => 'default', 'service.lists.position' => 3,
					'text.languageid' => 'de', 'text.type' => 'long', 'text.domain' => 'service',
					'text.label' => 'service_text6', 'text.content' => 'A long description for the service item', 'text.status' => 1
				]],
			],
		],
		'service/payment/unitpaymentcode' => [
			'service.type' => 'payment', 'service.code' => 'unitpaymentcode', 'service.label' => 'unitpaymentlabel',
			'service.provider' => 'PrePay', 'service.position' => 0, 'service.status' => 1,
			'service.config' => [
				'payment.url-success' => 'paymenturl'
			],
			'lists' => [
				'price' => [[
					'service.lists.type' => 'default', 'service.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'service',
					'price.label'=>'service/default/12.95/1.99', 'price.quantity' => 1, 'price.value' => '12.95',
					'price.costs' => '1.99', 'price.rebate' => '1.05', 'price.taxrate' => '19.00'
				], [
					'service.lists.type' => 'default', 'service.lists.position' => 0,
					'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.domain' => 'service',
					'price.label'=>'service/default/2.95/0.00', 'price.quantity' => 2, 'price.value' => '2.95',
					'price.costs' => '1.00', 'price.rebate' => '0.00', 'price.taxrate' => '19.00',
					'lists' => [
						'customer' => [[
							'price.lists.type' => 'test', 'price.lists.domain' => 'customer', 'price.lists.position' => 1,
							'ref' => 'unitCustomer001',
						], [
							'price.lists.type' => 'test', 'price.lists.domain' => 'customer', 'price.lists.position' => 2,
							'ref' => 'unitCustomer002',
						], [
							'price.lists.type' => 'test', 'price.lists.domain' => 'customer', 'price.lists.position' => 3,
							'price.lists.datestart' => '2002-01-01 00:00:00', 'price.lists.dateend' => '2006-12-31 23:59:59',
							'ref' => 'unitCustomer003',
						]]
					]
				]],
				'text' => [[
					'service.lists.type' => 'unittype1', 'service.lists.position' => 0,
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text1', 'text.content' => 'Unittest: Service text 1 de', 'text.status' => 1
				], [
					'service.lists.type' => 'unittype1', 'service.lists.position' => 1,
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text2', 'text.content' => 'Unittest: Service text 2 de', 'text.status' => 1
				], [
					'service.lists.type' => 'unittype1', 'service.lists.position' => 2,
					'service.lists.datestart' => '2008-02-17 12:34:58', 'service.lists.dateend' => '2010-01-01 23:59:59',
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text3', 'text.content' => 'Unittest: Service text 3 de', 'text.status' => 0
				], [
					'service.lists.type' => 'unittype1', 'service.lists.position' => 2,
					'text.languageid' => 'de', 'text.type' => 'serviceinformation', 'text.domain' => 'service',
					'text.label' => 'service_text3.1', 'text.content' => 'Unittest: Service text 3.1 de', 'text.status' => 0,
					'lists' => [
						'customer' => [[
							'text.lists.type' => 'test', 'text.lists.domain' => 'customer', 'text.lists.position' => 1,
							'ref' => 'unitCustomer001',
						], [
							'text.lists.type' => 'test', 'text.lists.domain' => 'customer', 'text.lists.position' => 2,
							'ref' => 'unitCustomer002',
						], [
							'text.lists.type' => 'test', 'text.lists.domain' => 'customer', 'text.lists.position' => 3,
							'text.lists.datestart' => '2002-01-01 00:00:00', 'text.lists.dateend' => '2006-12-31 23:59:59',
							'ref' => 'unitCustomer003',
						]]
					]
				]],
			],
		],
		'service/payment/directdebit' => [
			'service.type' => 'payment', 'service.code' => 'directdebit-test', 'service.label' => 'direct debit label',
			'service.provider' => 'DirectDebit', 'service.position' => 1, 'service.status' => 1,
			'service.config' => [],
		],
		'service/payment/paypalexpress' => [
			'service.type' => 'payment', 'service.code' => 'paypalexpress', 'service.label' => 'PayPalExpress',
			'service.provider' => 'PayPalExpress', 'service.position' => 2, 'service.status' => 1,
			'service.config' => [
				'paypalexpress.url-validate' => 'https://www.sandbox.paypal.com/webscr&cmd=_notify-validate',
				'paypalexpress.ApiUsername' => 'selling2_api1.metaways.de',
				'paypalexpress.AccountEmail' => 'selling2@metaways.de',
				'paypalexpress.ApiPassword' => '1387373899',
				'paypalexpress.ApiSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AwYOtMFx3HZuxFjoJ0gfSXrDHgnp',
				'paypalexpress.PaymentAction' => 'authorization',
				'paypalexpress.PaypalUrl' => 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
				'paypalexpress.ApiEndpoint' => 'https://api-3t.sandbox.paypal.com/nvp',
				'payment.url-success' => 'http://returnurl.com/updatesync.php',
				'payment.url-cancel' => 'http://cancelurl.com',
				'payment.url-update' => 'http://shopurl.com/ipn.php'
			],
		],
	]
	];
