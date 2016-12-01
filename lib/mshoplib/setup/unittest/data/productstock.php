<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'product/stock/type' => array(
		'unit_type1' => array( 'code' => 'unit_type1', 'label' => 'type label 1', 'status' => 1 ),
		'unit_type2' => array( 'code' => 'unit_type2', 'label' => 'type label 2', 'status' => 1 ),
		'unit_type3' => array( 'code' => 'unit_type3', 'label' => 'type label 3', 'status' => 1 ),
		'unit_type4' => array( 'code' => 'unit_type4', 'label' => 'type label 4', 'status' => 1 ),
		'unit_type5' => array( 'code' => 'unit_type5', 'label' => 'type label 5', 'status' => 1 ),
		'default' => array( 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'product/stock' => array(
		array( 'parentid' => 'product/CNE', 'typeid' => 'default', 'stocklevel' => 1000, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'parentid' => 'product/CNC', 'typeid' => 'default', 'stocklevel' => 1200, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'parentid' => 'product/U:MD', 'typeid' => 'unit_type3', 'stocklevel' => 200, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'parentid' => 'product/U:SD', 'typeid' => 'unit_type4', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:PD', 'typeid' => 'unit_type5', 'stocklevel' => 2000, 'backdate' => null ),
		array( 'parentid' => 'product/ABCD', 'typeid' => 'unit_type1', 'stocklevel' => 1100, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'parentid' => 'product/EFGH', 'typeid' => 'unit_type2', 'stocklevel' => 0, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'parentid' => 'product/IJKL', 'typeid' => 'unit_type3', 'stocklevel' => 3, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'parentid' => 'product/MNOP', 'typeid' => 'unit_type4', 'stocklevel' => null, 'backdate' => null ),
		array( 'parentid' => 'product/U:TESTP', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:TESTPSUB01', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:TESTSUB02', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:TESTSUB03', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:TESTSUB05', 'typeid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'parentid' => 'product/U:BUNDLE', 'typeid' => 'default', 'stocklevel' => 1000, 'backdate' => null ),
	)
);