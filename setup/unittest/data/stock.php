<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */

return [
	'stock/type' => [
		'product/unitstock' => ['stock.type.domain' => 'stock', 'stock.type.code' => 'unitstock', 'stock.type.label' => 'Unittest stock'],
		'product/default' => ['stock.type.domain' => 'stock', 'stock.type.code' => 'default', 'stock.type.label' => 'Standard'],
	],

	'stock' => [
		['prodcode' => 'CNE', 'stock.type' => 'default', 'stock.stocklevel' => 1000, 'stock.dateback' => '2010-04-01 00:00:00', 'stock.timeframe' => '4-5d'],
		['prodcode' => 'CNC', 'stock.type' => 'default', 'stock.stocklevel' => 1200, 'stock.dateback' => '2015-05-01 00:00:00'],
		['prodcode' => 'U:MD', 'stock.type' => 'unitstock', 'stock.stocklevel' => 200, 'stock.dateback' => '2006-06-01 00:00:00'],
		['prodcode' => 'U:SD', 'stock.type' => 'unitstock', 'stock.stocklevel' => 100, 'stock.dateback' => null, 'stock.timeframe' => '1-2d'],
		['prodcode' => 'U:PD', 'stock.type' => 'unitstock', 'stock.stocklevel' => 2000, 'stock.dateback' => null, 'stock.timeframe' => '1d'],
		['prodcode' => 'ABCD', 'stock.type' => 'unitstock', 'stock.stocklevel' => 1100, 'stock.dateback' => '2010-04-01 00:00:00'],
		['prodcode' => 'EFGH', 'stock.type' => 'unitstock', 'stock.stocklevel' => 0, 'stock.dateback' => '2015-05-01 00:00:00'],
		['prodcode' => 'IJKL', 'stock.type' => 'unitstock', 'stock.stocklevel' => 3, 'stock.dateback' => '2006-06-01 00:00:00'],
		['prodcode' => 'MNOP', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'U:TESTP', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.dateback' => null],
		['prodcode' => 'U:TESTPSUB01', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.dateback' => null, 'stock.timeframe' => '2d'],
		['prodcode' => 'U:TESTSUB02', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.dateback' => null, 'stock.timeframe' => '2-3d'],
		['prodcode' => 'U:TESTSUB03', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.dateback' => null, 'stock.timeframe' => '3d'],
		['prodcode' => 'U:TESTSUB05', 'stock.type' => 'default', 'stock.stocklevel' => 100, 'stock.dateback' => null, 'stock.timeframe' => '3-4d'],
		['prodcode' => 'U:BUNDLE', 'stock.type' => 'default', 'stock.stocklevel' => 1000, 'stock.dateback' => null, 'stock.timeframe' => '1w'],
		['prodcode' => 'bdl:zyx', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'bdl:EFG', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'bdl:HIJ', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'bdl:hal', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'bdl:EFX', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
		['prodcode' => 'bdl:HKL', 'stock.type' => 'unitstock', 'stock.stocklevel' => null, 'stock.dateback' => null],
	],
];
