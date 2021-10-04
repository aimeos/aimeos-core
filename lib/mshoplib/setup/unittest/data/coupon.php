<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'coupon' => [[
		'coupon.label' => 'Unit test fixed rebate', 'coupon.provider' => 'FixedRebate,Basket',
		'coupon.config' => ['fixedrebate.productcode' => 'U:MD', 'basket.total-value-min' => ['EUR' => '9.00'], 'fixedrebate.rebate' => '2.50'],
		'coupon.datestart' => '2002-01-01 00:00:00', 'coupon.dateend' => '2100-12-31 00:00:00',
		'codes' => [[
			'coupon.code.code' => '5678', 'coupon.code.count' => 2000000,
			'coupon.code.datestart' => '2000-01-01 00:00:00', 'coupon.code.dateend' => '2004-12-21 23:59:59'
		]]
	], [
		'coupon.label' => 'Unit test percent rebate', 'coupon.provider' => 'PercentRebate,Basket',
		'coupon.config' => ['basket.total-value-min' => ['EUR' => '9.00'], 'percentrebate.productcode' => 'U:MD', 'percentrebate.rebate' => '10'],
		'codes' => [[
			'coupon.code.code' => '90AB', 'coupon.code.count' => 2000000
		]]
	], [
		'coupon.label' => 'Unit test free shipping', 'coupon.provider' => 'FreeShipping,Basket',
		'coupon.config' => ['basket.total-value-min' => ['EUR' => '9.00'], 'freeshipping.productcode' => 'U:SD'],
		'codes' => [[
			'coupon.code.code' => 'CDEF', 'coupon.code.count' => 2000000
		]]
	], [
		'coupon.label' => 'Unit test present', 'coupon.provider' => 'Present,Basket',
		'coupon.config' => ['basket.total-value-min' => ['EUR' => '9.00'], 'present.productcode' => 'U:PD', 'present.quantity' => 2],
		'codes' => [[
			'coupon.code.code' => 'GHIJ', 'coupon.code.count' => 2000000
		]]
	], [
		'coupon.label' => 'Unit test example', 'coupon.provider' => 'None,Basket',
		'coupon.config' => ['basket.total-value-min' => ['EUR' => '9.00']],
		'codes' => [[
			'coupon.code.code' => 'OPQR', 'coupon.code.count' => 2000000
		]]
	], [
		'coupon.label' => 'Unit test voucher', 'coupon.provider' => 'Voucher,Once',
		'coupon.config' => ['voucher.productcode' => 'U:MD'],
		'codes' => [[
			'coupon.code.code' => 'STUV', 'coupon.code.count' => 1
		]]
	]],
];
