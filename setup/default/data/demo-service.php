<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */

return [

	// delivery items
	[
		'service.code' => 'demo-pickup', 'service.type' => 'delivery', 'service.label' => 'Click & Collect',
		'service.provider' => 'Standard,Time,Supplier', 'service.position' => 0, 'service.status' => 1, 'service.config' => [],
		'text' => [
			[
				'text.label' => 'Demo short/de: Abholung vor Ort',
				'text.content' => 'Abholung vor Ort',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Abholung vor Ort',
				'text.content' => 'Abholung vor Ort bei einem unserer Läden',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Local pick-up',
				'text.content' => 'Local pick-up',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: Local pick-up',
				'text.content' => 'Pick-up at one of our local stores',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Click&Collect',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '0.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Click&Collect',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '0.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: pickup.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/pickup.png',
				'media.previews' => [1 => 'https://aimeos.org/media/service/pickup.png'],
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-dhl', 'service.type' => 'delivery', 'service.label' => 'DHL',
		'service.provider' => 'Standard', 'service.config' => [], 'service.position' => 1, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'text.content' => 'Lieferung innerhalb von drei Tagen.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'text.content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Delivery within three days',
				'text.content' => 'Delivery within three days',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: The parcel is usually delivered',
				'text.content' => 'The parcel is usually delivered within three working days',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: DHL',
				'price.value' => '0.00', 'price.costs' => '5.90', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: DHL',
				'price.value' => '0.00', 'price.costs' => '7.90', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: dhl.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/dhl.png',
				'media.previews' => 'https://aimeos.org/media/service/dhl.png',
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-dhlexpress', 'service.type' => 'delivery', 'service.label' => 'DHL Express',
		'service.provider' => 'Standard', 'service.config' => [], 'service.position' => 2, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo short/de: Lieferung am nächsten Tag',
				'text.content' => 'Lieferung am nächsten Tag.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Bei Bestellungen bis 16:00 Uhr',
				'text.content' => 'Bei Bestellungen bis 16:00 Uhr erfolgt die Lieferung am nächsten Werktag',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Delivery on the next day',
				'text.content' => 'Delivery on the next day',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: If you order till 16 o\'clock',
				'text.content' => 'If you order till 16 o\'clock the delivery will be on the next working day',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: DHL',
				'price.value' => '0.00', 'price.costs' => '11.90', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: DHL',
				'price.value' => '0.00', 'price.costs' => '15.90', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: dhl-express.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/dhl-express.png',
				'media.previews' => 'https://aimeos.org/media/service/dhl-express.png',
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-fedex', 'service.type' => 'delivery', 'service.label' => 'Fedex',
		'service.provider' => 'Standard', 'service.config' => [], 'service.position' => 3, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'text.content' => 'Lieferung innerhalb von drei Tagen.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'text.content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Delivery within three days',
				'text.content' => 'Delivery within three days',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: The parcel is usually delivered',
				'text.content' => 'The parcel is usually delivered within three working days',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Fedex',
				'price.value' => '0.00', 'price.costs' => '6.90', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Fedex',
				'price.value' => '0.00', 'price.costs' => '8.50', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: fedex.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/fedex.png',
				'media.previews' => 'https://aimeos.org/media/service/fedex.png',
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-tnt', 'service.type' => 'delivery', 'service.label' => 'TNT',
		'service.provider' => 'Standard', 'service.config' => [], 'service.position' => 4, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo short/de: Lieferung innerhalb von drei Tagen',
				'text.content' => 'Lieferung innerhalb von drei Tagen.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Die Lieferung erfolgt in der Regel',
				'text.content' => 'Die Lieferung erfolgt in der Regel innerhalb von drei Werktagen',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Delivery within three days',
				'text.content' => 'Delivery within three days',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: The parcel is usually delivered',
				'text.content' => 'The parcel is usually delivered within three working days',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: TNT',
				'price.value' => '0.00', 'price.costs' => '8.90', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: TNT',
				'price.value' => '0.00', 'price.costs' => '12.90', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: tnt.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/tnt.png',
				'media.previews' => 'https://aimeos.org/media/service/tnt.png',
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],

	// payment items
	[
		'service.code' => 'demo-invoice', 'service.type' => 'payment', 'service.label' => 'Invoice',
		'service.provider' => 'PostPay', 'service.config' => [], 'service.position' => 0, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Rechnung', 'text.content' => 'Rechnung',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/de: Zahlung per Rechnung',
				'text.content' => 'Zahlung per Rechnung innerhalb von 14 Tagen.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Bitte überweisen Sie den Betrag',
				'text.content' => 'Bitte überweisen Sie den Betrag innerhalb von 14 Tagen an BIC: XXX, IBAN: YYY',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Pay by invoice',
				'text.content' => 'Pay by invoice within 14 days',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: Please transfer the money',
				'text.content' => 'Please transfer the money within 14 days to BIC: XXX, IBAN: YYY',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Invoice',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Invoice',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: payment-in-advance.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/payment-in-advance.png',
				'media.previews' => 'https://aimeos.org/media/service/payment-in-advance.png',
				'media.type' => 'icon', 'media.languageid' => 'de', 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-sepa', 'service.type' => 'payment', 'service.label' => 'Direct debit',
		'service.provider' => 'DirectDebit', 'service.config' => [], 'service.position' => 1, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Lastschrift', 'text.content' => 'Lastschrift',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/de: Abbuchung vom angegebenen Konto',
				'text.content' => 'Abbuchung vom angegebenen Konto.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Der Betrag wird in den nächsten 1-3 Tagen',
				'text.content' => 'Der Betrag wird in den nächsten 1-3 Tagen von Ihrem Konto abgebucht',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Payment via your bank account',
				'text.content' => 'Payment via your bank account',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: The money will be collected',
				'text.content' => 'The money will be collected from your bank account within 1-3 days',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Direct debit',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Direct debit',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: sepa.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/sepa.png',
				'media.previews' => 'https://aimeos.org/media/service/sepa.png',
				'media.type' => 'icon', 'media.languageid' => 'de', 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'media.label' => 'Demo: direct-debit.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/direct-debit.png',
				'media.previews' => 'https://aimeos.org/media/service/direct-debit.png',
				'media.type' => 'icon', 'languageid' => 'en', 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-paypal', 'service.type' => 'payment', 'service.label' => 'PayPal',
		'service.provider' => 'PayPalExpress', 'service.position' => 2, 'service.status' => 1,
		'service.config' => [
			'paypalexpress.AccountEmail' => 'selling2@metaways.de',
			'paypalexpress.ApiUsername' => 'unit_1340199666_biz_api1.yahoo.de',
			'paypalexpress.ApiPassword' => '1340199685',
			'paypalexpress.ApiSignature' => 'A34BfbVoMVoHt7Sf8BlufLXS8tKcAVxmJoDiDUgBjWi455pJoZXGoJ87',
			'paypalexpress.PaypalUrl' => 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&useraction=commit&token=%1$s',
			'paypalexpress.ApiEndpoint' => 'https://api-3t.sandbox.paypal.com/nvp',
		],
		'text' => [
			[
				'text.label' => 'Demo short/de: Zahlung mit ihrem PayPal Konto',
				'text.content' => 'Zahlung mit PayPal',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Einfache Bezahlung mit Ihrem PayPal Konto',
				'text.content' => 'Einfache Bezahlung mit Ihrem PayPal Konto.',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Payment via your PayPal account',
				'text.content' => 'Payment via PayPal',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: Easy and secure payment with your PayPal account',
				'text.content' => 'Easy and secure payment with your PayPal account',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: PayPal',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: PayPal',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: paypal.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/paypal.png',
				'media.previews' => 'https://aimeos.org/media/service/paypal.png',
				'media.type' => 'icon', 'media.languageid' => null, 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-cashondelivery', 'service.type' => 'payment', 'service.label' => 'Cash on delivery',
		'service.provider' => 'PostPay', 'service.config' => [], 'service.position' => 3, 'service.status' => 1,
		'text' => [
			[
				'text.label' => 'Demo name/de: Nachnahme', 'text.content' => 'Nachnahme',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/de: Zahlung bei Lieferung',
				'text.content' => 'Zahlung bei Lieferung.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Die Bezahlung erfolgt bei Übergabe der Ware',
				'text.content' => 'Die Bezahlung erfolgt bei Übergabe der Ware',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: Pay cash on delivery of the parcel',
				'text.content' => 'Pay cash on delivery of the parcel',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Cache on delivery',
				'price.value' => '0.00', 'price.costs' => '8.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Cache on delivery',
				'price.value' => '0.00', 'price.costs' => '12.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: dhl-cod.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/dhl-cod.png',
				'media.previews' => 'https://aimeos.org/media/service/dhl-cod.png',
				'media.type' => 'icon', 'media.languageid' => 'de', 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
	[
		'service.code' => 'demo-prepay', 'service.type' => 'payment', 'service.label' => 'Prepayment',
		'service.provider' => 'PrePay,Reduction', 'service.position' => 4, 'service.status' => 1,
		'service.config' => [
			'reduction.basket-value-min' => ['EUR' => '200.00'],
			'reduction.percent' => 3,
		],
		'text' => [
			[
				'text.label' => 'Demo name/de: Vorauskasse', 'text.content' => 'Vorauskasse',
				'text.type' => 'name', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/de: Versand der Ware nach Zahlungseingang',
				'text.content' => '3% Rabatt, Versand der Ware nach Zahlungseingang.',
				'text.type' => 'short', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 1, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/de: Bitte überweisen Sie den Betrag',
				'text.content' => 'Bitte überweisen Sie den Betrag an BIC: XXX, IBAN: YYY',
				'text.type' => 'long', 'text.languageid' => 'de', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 2, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo short/en: The parcel will be shipped after the payment has been received',
				'text.content' => '3% discount, the parcel will be shipped after the payment has been received',
				'text.type' => 'short', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 3, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'text.label' => 'Demo long/en: Please transfer the money',
				'text.content' => 'Please transfer the money to BIC: XXX, IBAN: YYY',
				'text.type' => 'long', 'text.languageid' => 'en', 'text.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 4, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'price' => [
			[
				'price.label' => 'Demo: Prepayment',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '20.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'EUR', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
			[
				'price.label' => 'Demo: Prepayment',
				'price.value' => '0.00', 'price.costs' => '0.00', 'price.rebate' => '0.00', 'price.taxrate' => '10.00',
				'price.quantity' => 1, 'price.type' => 'default', 'price.currencyid' => 'USD', 'price.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
		'media' => [
			[
				'media.label' => 'Demo: payment-in-advance-alternative.png', 'media.mimetype' => 'image/png',
				'media.url' => 'https://aimeos.org/media/service/payment-in-advance-alternative.png',
				'media.previews' => 'https://aimeos.org/media/service/payment-in-advance-alternative.png',
				'media.type' => 'icon', 'media.languageid' => 'de', 'media.status' => 1,
				'servcie.list.type' => 'default', 'servcie.list.position' => 0, 'servcie.list.config' => [],
				'servcie.list.start' => null, 'servcie.list.end' => null, 'servcie.list.status' => 1
			],
		],
	],
];
