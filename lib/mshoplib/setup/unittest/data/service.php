<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'service/type' => array (
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

	'service' => array (
		'service/delivery/unitcode' => array (
			'pos' => 0,
			'typeid' => 'service/delivery',
			'code' => 'unitcode',
			'label' => 'unitlabel',
			'provider' => 'Default',
			'config' => array(
				'default.url' => 'deliveryurl'
			),
			'status' => 1
		),
		'service/payment/unitcode' => array (
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
		'service/payment/directdebit' => array (
			'pos' => 1,
			'typeid' => 'service/payment',
			'code' => 'directdebit-test',
			'label' => 'direct debit label',
			'provider' => 'DirectDebit',
			'config' => array(),
			'status' => 1
		),
		'service/payment/paypalexpress' => array (
			'pos' => 2,
			'typeid' => 'service/payment',
			'code' => 'paypalexpress',
			'label' => 'PayPalExpress',
			'provider' => 'PayPalExpress',
			'config' => array(
				'paypal.Ipn' => 'https://www.sandbox.paypal.com/webscr&cmd=_notify-validate',
				'paypalexpress.ApiUsername' => 'selling_api1.metaways.de',
				'paypalexpress.ApiPassword' => '1386850805',
				'paypalexpress.ApiSignature' => 'AuXHw.oeakbZYO3vDxAN91eJ-DvSAPR5bKlxkH4Upsd1qc1gbP3BhyGf',
				'paypalexpress.PaymentAction' => 'authorization',
				'paypalexpress.PaypalUrl' => 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
				'paypalexpress.ApiEndpoint' => 'https://api-3t.sandbox.paypal.com/nvp',
				'payment.url-success' => 'http://returnurl.com/updatesync.php',
				'payment.url-cancel' => 'http://cancelurl.com',
			),
			'status' => 1
		),
	)
);