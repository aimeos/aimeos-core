<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'stock/type' => array(
		'unit_type1' => array( 'domain' => 'product', 'code' => 'unit_type1', 'label' => 'type label 1', 'status' => 1 ),
		'unit_type2' => array( 'domain' => 'product', 'code' => 'unit_type2', 'label' => 'type label 2', 'status' => 1 ),
		'unit_type3' => array( 'domain' => 'product', 'code' => 'unit_type3', 'label' => 'type label 3', 'status' => 1 ),
		'unit_type4' => array( 'domain' => 'product', 'code' => 'unit_type4', 'label' => 'type label 4', 'status' => 1 ),
		'unit_type5' => array( 'domain' => 'product', 'code' => 'unit_type5', 'label' => 'type label 5', 'status' => 1 ),
		'default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'stock' => array(
		array( 'productcode' => 'CNE', 'typeid' => 'default', 'stocklevel' => 1000, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'productcode' => 'CNC', 'typeid' => 'default', 'stocklevel' => 1200, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'productcode' => 'U:MD', 'typeid' => 'unit_type3', 'stocklevel' => 200, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'productcode' => 'U:SD', 'typeid' => 'unit_type4', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:PD', 'typeid' => 'unit_type5', 'stocklevel' => 2000, 'backdate' => null ),
		array( 'productcode' => 'ABCD', 'typeid' => 'unit_type1', 'stocklevel' => 1100, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'productcode' => 'EFGH', 'typeid' => 'unit_type2', 'stocklevel' => 0, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'productcode' => 'IJKL', 'typeid' => 'unit_type3', 'stocklevel' => 3, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'productcode' => 'MNOP', 'typeid' => 'unit_type4', 'stocklevel' => null, 'backdate' => null ),
		array( 'productcode' => 'U:TESTP', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:TESTPSUB01', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:TESTSUB02', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:TESTSUB03', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:TESTSUB05', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'productcode' => 'U:BUNDLE', 'typeid' => 'default', 'stocklevel' => 1000, 'backdate' => null ),
	)
);