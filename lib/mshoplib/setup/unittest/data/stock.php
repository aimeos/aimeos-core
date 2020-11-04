<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return [
	'stock/type' => [
		'product/unit_type1' => ['stock.type.domain' => 'product', 'stock.type.code' => 'unit_type1', 'stock.type.label' => 'type label 1', 'stock.type.status' => 1],
		'product/unit_type2' => ['stock.type.domain' => 'product', 'stock.type.code' => 'unit_type2', 'stock.type.label' => 'type label 2', 'stock.type.status' => 1],
		'product/unit_type3' => ['stock.type.domain' => 'product', 'stock.type.code' => 'unit_type3', 'stock.type.label' => 'type label 3', 'stock.type.status' => 1],
		'product/unit_type4' => ['stock.type.domain' => 'product', 'stock.type.code' => 'unit_type4', 'stock.type.label' => 'type label 4', 'stock.type.status' => 1],
		'product/unit_type5' => ['stock.type.domain' => 'product', 'stock.type.code' => 'unit_type5', 'stock.type.label' => 'type label 5', 'stock.type.status' => 1],
		'product/default' => ['stock.type.domain' => 'product', 'stock.type.code' => 'default', 'stock.type.label' => 'Standard', 'stock.type.status' => 1],
	],

	'stock' => [
		['prodcode' => 'CNE', 'stock.type' => 'default', 'stock.stocklevel' => 1000, 'stock.backdate' => '2010-04-01 00:00:00', 'stock.timeframe' => '4-5d'],
		['prodcode' => 'CNC', 'stock.type' => 'default', 'stock.stocklevel' => 1200, 'stock.backdate' => '2015-05-01 00:00:00'],
		['prodcode' => 'U:MD', 'stock.type' => 'unit_type3', 'stock.stocklevel' => 200, 'stock.backdate' => '2006-06-01 00:00:00'],
		['prodcode' => 'U:SD', 'stock.type' => 'unit_type4', 'stock.stocklevel' => 100, 'stock.backdate' => null, 'stock.timeframe' => '1-2d'],
		['prodcode' => 'U:PD', 'stock.type' => 'unit_type5', 'stock.stocklevel' => 2000, 'stock.backdate' => null, 'stock.timeframe' => '1d'],
		['prodcode' => 'ABCD', 'stock.type' => 'unit_type1', 'stock.stocklevel' => 1100, 'stock.backdate' => '2010-04-01 00:00:00'],
		['prodcode' => 'EFGH', 'stock.type' => 'unit_type2', 'stock.stocklevel' => 0, 'stock.backdate' => '2015-05-01 00:00:00'],
		['prodcode' => 'IJKL', 'stock.type' => 'unit_type3', 'stock.stocklevel' => 3, 'stock.backdate' => '2006-06-01 00:00:00'],
		['prodcode' => 'MNOP', 'stock.type' => 'unit_type4', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'U:TESTP', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.backdate' => null],
		['prodcode' => 'U:TESTPSUB01', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.backdate' => null, 'stock.timeframe' => '2d'],
		['prodcode' => 'U:TESTSUB02', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.backdate' => null, 'stock.timeframe' => '2-3d'],
		['prodcode' => 'U:TESTSUB03', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.backdate' => null, 'stock.timeframe' => '3d'],
		['prodcode' => 'U:TESTSUB05', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.backdate' => null, 'stock.timeframe' => '3-4d'],
		['prodcode' => 'U:BUNDLE', 'stock.type' => 'default', 'stock.stocklevel' => 1000, 'stock.backdate' => null, 'stock.timeframe' => '1w'],
		['prodcode' => 'bdl:zyx', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'bdl:EFG', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'bdl:HIJ', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'bdl:hal', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'bdl:EFX', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
		['prodcode' => 'bdl:HKL', 'stock.type' => 'unit_type1', 'stock.stocklevel' => null, 'stock.backdate' => null],
	],
];
