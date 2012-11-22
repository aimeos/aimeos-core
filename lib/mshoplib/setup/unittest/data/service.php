<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: service.php 1163 2012-08-28 09:25:31Z doleiynyk $
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
				'url' => 'deliveryurl'
			),
			'status' => 1
		),
		'service/payment/unitcode' => array (
			'pos' => 0,
			'typeid' =>
			'service/payment',
			'code' => 'unitpaymentcode',
			'label' => 'unitpaymentlabel',
			'provider' => 'PrePay',
			'config' => array(
				'url' => 'paymenturl'
			),
			'status' => 1
		),
		'service/payment/paypalexpress' => array (
			'pos' => 0,
			'typeid' => 'service/payment',
			'code' => 'paypalexpress',
			'label' => 'PayPalExpress',
			'provider' => 'PayPalExpress',
			'config' => array(
				'ApiUsername' => 'unit_1340199666_biz_api1.yahoo.de',
				'ApiPassword' => '1340199685',
				'ApiSignature' => 'A34BfbVoMVoHt7Sf8BlufLXS8tKcAVxmJoDiDUgBjWi455pJoZXGoJ87',
				'CancelUrl' => 'http://cancelurl.com',
				'ReturnUrl' => 'http://returnurl.com',
				'PaymentAction' => 'authorization',
				'PaypalUrl' => 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=',
				'ApiEndpoint' => 'https://api-3t.sandbox.paypal.com/nvp'
			),
			'status' => 1 ),
	)
);