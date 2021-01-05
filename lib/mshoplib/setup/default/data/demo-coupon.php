<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return array(
	array(
		'label' => 'demo-voucher', 'provider' => 'Voucher', 'status' => 1,
		'datestart' => null, 'dateend' => null,
		'config' => array(
			'voucher.productcode' => 'demo-rebate',
		),
		'codes' => [],
	),
	array(
		'label' => 'demo-fixed', 'provider' => 'FixedRebate,BasketValues', 'status' => 1,
		'datestart' => null, 'dateend' => null,
		'config' => array(
			'fixedrebate.productcode' => 'demo-rebate',
			'fixedrebate.rebate' => array( 'EUR' => 125.00, 'USD' => 150.00 ),
			'basketvalues.total-value-min' => array( 'EUR' => 125.00, 'USD' => 150.00 ),
		),
		'codes' => array(
			array(
				'code' => 'fixed', 'count' => 1000,
				'datestart' => null, 'dateend' => null,
			),
		),
	),
	array(
		'label' => 'demo-percent', 'provider' => 'PercentRebate', 'status' => 1,
		'datestart' => null, 'dateend' => null,
		'config' => array(
			'percentrebate.productcode' => 'demo-rebate',
			'percentrebate.rebate' => '10',
		),
		'codes' => array(
			array(
				'code' => 'percent', 'count' => 1000,
				'datestart' => null, 'dateend' => null,
			),
		),
	),
);
