<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	array(
		'label' => 'demo-fixed', 'provider' => 'FixedRebate', 'status' => 1,
		'datestart' => null, 'dateend' => null,
		'config' => array(
			'fixedrebate.productcode' => 'demo-rebate',
			'fixedrebate.rebate' => array( 'EUR' => 125.00, 'USD' => 150.00 ),
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
