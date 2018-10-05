<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array(
	'stock/type' => array(
		'product/unit_type1' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'unit_type1', 'stock.type.label' => 'type label 1', 'stock.type.status' => 1 ),
		'product/unit_type2' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'unit_type2', 'stock.type.label' => 'type label 2', 'stock.type.status' => 1 ),
		'product/unit_type3' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'unit_type3', 'stock.type.label' => 'type label 3', 'stock.type.status' => 1 ),
		'product/unit_type4' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'unit_type4', 'stock.type.label' => 'type label 4', 'stock.type.status' => 1 ),
		'product/unit_type5' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'unit_type5', 'stock.type.label' => 'type label 5', 'stock.type.status' => 1 ),
		'product/default' => array( 'stock.type.domain' => 'product', 'stock.type.code' => 'default', 'stock.type.label' => 'Standard', 'stock.type.status' => 1 ),
	),

	'stock' => array(
		array( 'stock.productcode' => 'CNE', 'stock.type' => 'product/default', 'stock.stocklevel' => 1000, 'stock.backdate' => '2010-04-01 00:00:00' ),
		array( 'stock.productcode' => 'CNC', 'stock.type' => 'product/default', 'stock.stocklevel' => 1200, 'stock.backdate' => '2015-05-01 00:00:00' ),
		array( 'stock.productcode' => 'U:MD', 'stock.type' => 'product/unit_type3', 'stock.stocklevel' => 200, 'stock.backdate' => '2006-06-01 00:00:00' ),
		array( 'stock.productcode' => 'U:SD', 'stock.type' => 'product/unit_type4', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:PD', 'stock.type' => 'product/unit_type5', 'stock.stocklevel' => 2000, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'ABCD', 'stock.type' => 'product/unit_type1', 'stock.stocklevel' => 1100, 'stock.backdate' => '2010-04-01 00:00:00' ),
		array( 'stock.productcode' => 'EFGH', 'stock.type' => 'product/unit_type2', 'stock.stocklevel' => 0, 'stock.backdate' => '2015-05-01 00:00:00' ),
		array( 'stock.productcode' => 'IJKL', 'stock.type' => 'product/unit_type3', 'stock.stocklevel' => 3, 'stock.backdate' => '2006-06-01 00:00:00' ),
		array( 'stock.productcode' => 'MNOP', 'stock.type' => 'product/unit_type4', 'stock.stocklevel' => null, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:TESTP', 'stock.type' => 'product/default', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:TESTPSUB01', 'stock.type' => 'product/default', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:TESTSUB02', 'stock.type' => 'product/default', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:TESTSUB03', 'stock.type' => 'product/default', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:TESTSUB05', 'stock.type' => 'product/default', 'stock.stocklevel' => 100, 'stock.backdate' => null ),
		array( 'stock.productcode' => 'U:BUNDLE', 'stock.type' => 'product/default', 'stock.stocklevel' => 1000, 'stock.backdate' => null ),
	)
);