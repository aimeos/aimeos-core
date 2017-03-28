<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(

	// delivery items
	array(
		'code' => 'demo-dhl', 'type' => 'delivery', 'label' => 'DHL',
		'provider' => 'Manual,Reduction', 'position' => 0, 'status' => 1,
		'config' => array(
			'reduction.basket-value-min' => array( 'EUR' => '200.00' ),
			'reduction.percent' => 100,
		),
		'text' => array(
			array(
				'label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'content' => 'Lieferung innerhalb von drei Tagen.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Delivery within three days',
				'content' => 'Delivery within three days',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: The parcel is usually delivered',
				'content' => 'The parcel is usually delivered within three working days',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: DHL',
				'value' => '0.00', 'costs' => '5.90', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: DHL',
				'value' => '0.00', 'costs' => '7.90', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: dhl.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/dhl.png',
				'preview' => 'http://demo.aimeos.org/media/service/dhl.png',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-dhlexpress', 'type' => 'delivery', 'label' => 'DHL Express',
		'provider' => 'Manual', 'config' => [], 'position' => 1, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo short/de: Lieferung am nächsten Tag',
				'content' => 'Lieferung am nächsten Tag.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Bei Bestellungen bis 16:00 Uhr',
				'content' => 'Bei Bestellungen bis 16:00 Uhr erfolgt die Lieferung am nächsten Werktag',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Delivery on the next day',
				'content' => 'Delivery on the next day',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: If you order till 16 o\'clock',
				'content' => 'If you order till 16 o\'clock the delivery will be on the next working day',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: DHL',
				'value' => '0.00', 'costs' => '11.90', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: DHL',
				'value' => '0.00', 'costs' => '15.90', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: dhl-express.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/dhl-express.png',
				'preview' => 'http://demo.aimeos.org/media/service/dhl-express.png',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-fedex', 'type' => 'delivery', 'label' => 'Fedex',
		'provider' => 'Manual', 'config' => [], 'position' => 2, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'content' => 'Lieferung innerhalb von drei Tagen.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Delivery within three days',
				'content' => 'Delivery within three days',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: The parcel is usually delivered',
				'content' => 'The parcel is usually delivered within three working days',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Fedex',
				'value' => '0.00', 'costs' => '6.90', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Fedex',
				'value' => '0.00', 'costs' => '8.50', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: fedex.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/fedex.png',
				'preview' => 'http://demo.aimeos.org/media/service/fedex.png',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-tnt', 'type' => 'delivery', 'label' => 'TNT',
		'provider' => 'Manual', 'config' => [], 'position' => 4, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'content' => 'Lieferung innerhalb von drei Tagen.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Delivery within three days',
				'content' => 'Delivery within three days',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: The parcel is usually delivered',
				'content' => 'The parcel is usually delivered within three working days',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: TNT',
				'value' => '0.00', 'costs' => '8.90', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: TNT',
				'value' => '0.00', 'costs' => '12.90', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: tnt.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/tnt.png',
				'preview' => 'http://demo.aimeos.org/media/service/tnt.png',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),

	// payment items
	array(
		'code' => 'demo-invoice', 'type' => 'payment', 'label' => 'Invoice',
		'provider' => 'PostPay', 'config' => [], 'position' => 0, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Rechnung', 'content' => 'Rechnung',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Zahlung per Rechnung',
				'content' => 'Zahlung per Rechnung innerhalb von 14 Tagen.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Bitte überweisen Sie den Betrag',
				'content' => 'Bitte überweisen Sie den Betrag innerhalb von 14 Tagen an BIC: XXX, IBAN: YYY',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Pay by invoice',
				'content' => 'Pay by invoice within 14 days',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Please transfer the money',
				'content' => 'Please transfer the money within 14 days to BIC: XXX, IBAN: YYY',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Invoice',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Invoice',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: payment-in-advance.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/payment-in-advance.png',
				'preview' => 'http://demo.aimeos.org/media/service/payment-in-advance.png',
				'type' => 'default', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-sepa', 'type' => 'payment', 'label' => 'Direct debit',
		'provider' => 'DirectDebit', 'config' => [], 'position' => 1, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Lastschrift', 'content' => 'Lastschrift',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Abbuchung vom angegebenen Konto',
				'content' => 'Abbuchung vom angegebenen Konto.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Der Betrag wird in den nächsten 1-3 Tagen',
				'content' => 'Der Betrag wird in den nächsten 1-3 Tagen von Ihrem Konto abgebucht',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Payment via your bank account',
				'content' => 'Payment via your bank account',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: The money will be collected',
				'content' => 'The money will be collected from your bank account within 1-3 days',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Direct debit',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Direct debit',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: sepa.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/sepa.png',
				'preview' => 'http://demo.aimeos.org/media/service/sepa.png',
				'type' => 'default', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: direct-debit.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/direct-debit.png',
				'preview' => 'http://demo.aimeos.org/media/service/direct-debit.png',
				'type' => 'default', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-paypal', 'type' => 'payment', 'label' => 'PayPal',
		'provider' => 'PayPalExpress', 'position' => 2, 'status' => 1,
		'config' => array(
			'paypalexpress.AccountEmail' => 'selling2@metaways.de',
			'paypalexpress.ApiUsername' => 'unit_1340199666_biz_api1.yahoo.de',
			'paypalexpress.ApiPassword' => '1340199685',
			'paypalexpress.ApiSignature' => 'A34BfbVoMVoHt7Sf8BlufLXS8tKcAVxmJoDiDUgBjWi455pJoZXGoJ87',
			'paypalexpress.PaypalUrl' => 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
			'paypalexpress.ApiEndpoint' => 'https://api-3t.sandbox.paypal.com/nvp',
		),
		'text' => array(
			array(
				'label' => 'Demo short/de: Zahlung mit ihrem PayPal Konto',
				'content' => 'Zahlung mit PayPal',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Einfache Bezahlung mit Ihrem PayPal Konto',
				'content' => 'Einfache Bezahlung mit Ihrem PayPal Konto.',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Payment via your PayPal account',
				'content' => 'Payment via PayPal',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Easy and secure payment with your PayPal account',
				'content' => 'Easy and secure payment with your PayPal account',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: PayPal',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: PayPal',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: paypal.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/paypal.png',
				'preview' => 'http://demo.aimeos.org/media/service/paypal.png',
				'type' => 'default', 'languageid' => null, 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-cashondelivery', 'type' => 'payment', 'label' => 'Cash on delivery',
		'provider' => 'PostPay', 'config' => [], 'position' => 3, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Nachnahme', 'content' => 'Nachnahme',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Zahlung bei Lieferung',
				'content' => 'Zahlung bei Lieferung.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Die Bezahlung erfolgt bei Übergabe der Ware',
				'content' => 'Die Bezahlung erfolgt bei Übergabe der Ware',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: Pay cash on delivery of the parcel',
				'content' => 'Pay cash on delivery of the parcel',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Cache on delivery',
				'value' => '0.00', 'costs' => '8.00', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Cache on delivery',
				'value' => '0.00', 'costs' => '12.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: dhl-cod.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/dhl-cod.png',
				'preview' => 'http://demo.aimeos.org/media/service/dhl-cod.png',
				'type' => 'default', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
	array(
		'code' => 'demo-prepay', 'type' => 'payment', 'label' => 'Prepayment',
		'provider' => 'PrePay', 'config' => [], 'position' => 4, 'status' => 1,
		'text' => array(
			array(
				'label' => 'Demo name/de: Vorauskasse', 'content' => 'Vorauskasse',
				'type' => 'name', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/de: Versand der Ware nach Zahlungseingang',
				'content' => 'Versand der Ware nach Zahlungseingang.',
				'type' => 'short', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 1, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/de: Bitte überweisen Sie den Betrag',
				'content' => 'Bitte überweisen Sie den Betrag an BIC: XXX, IBAN: YYY',
				'type' => 'long', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 2, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo short/en: The parcel will be shipped after the payment has been received',
				'content' => 'The parcel will be shipped after the payment has been received',
				'type' => 'short', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 3, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo long/en: Please transfer the money',
				'content' => 'Please transfer the money to BIC: XXX, IBAN: YYY',
				'type' => 'long', 'languageid' => 'en', 'status' => 1,
				'list-type' => 'default', 'list-position' => 4, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'price' => array(
			array(
				'label' => 'Demo: Prepayment',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '20.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'EUR', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
			array(
				'label' => 'Demo: Prepayment',
				'value' => '0.00', 'costs' => '0.00', 'rebate' => '0.00', 'taxrate' => '10.00',
				'quantity' => 1, 'type' => 'default', 'currencyid' => 'USD', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
		'media' => array(
			array(
				'label' => 'Demo: payment-in-advance-alternative.png', 'mimetype' => 'image/png',
				'url' => 'http://demo.aimeos.org/media/service/payment-in-advance-alternative.png',
				'preview' => 'http://demo.aimeos.org/media/service/payment-in-advance-alternative.png',
				'type' => 'default', 'languageid' => 'de', 'status' => 1,
				'list-type' => 'default', 'list-position' => 0, 'list-config' => [],
				'list-start' => null, 'list-end' => null, 'list-status' => 1
			),
		),
	),
);
