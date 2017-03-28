<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'service/type' => array(
		'service/payment' => array(
			'domain' => 'service',
			'code' => 'payment',
			'label' => 'Payment',
			'status' => 1
		),
		'service/delivery' => array(
			'domain' => 'service',
			'code' => 'delivery',
			'label' => 'Delivery',
			'status' => 1
		),
	),

	'service' => array(
		'service/delivery/unitcode' => array(
			'pos' => 0,
			'typeid' => 'service/delivery',
			'code' => 'unitcode',
			'label' => 'unitlabel',
			'provider' => 'Standard',
			'config' => array(
				'default.url' => 'deliveryurl'
			),
			'status' => 1
		),
		'service/payment/unitcode' => array(
			'pos' => 0,
			'typeid' => 'service/payment',
			'code' => 'unitpaymentcode',
			'label' => 'unitpaymentlabel',
			'provider' => 'PrePay',
			'config' => array(
				'payment.url-success' => 'paymenturl'
			),
			'status' => 1
		),
		'service/payment/directdebit' => array(
			'pos' => 1,
			'typeid' => 'service/payment',
			'code' => 'directdebit-test',
			'label' => 'direct debit label',
			'provider' => 'DirectDebit',
			'config' => [],
			'status' => 1
		),
		'service/payment/paypalexpress' => array(
			'pos' => 2,
			'typeid' => 'service/payment',
			'code' => 'paypalexpress',
			'label' => 'PayPalExpress',
			'provider' => 'PayPalExpress',
			'config' => array(
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
			),
			'status' => 1
		),
	)
);