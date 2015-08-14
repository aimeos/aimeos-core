<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'product/stock/warehouse' => array(
		'unit_warehouse1' => array( 'code' => 'unit_warehouse1', 'label' => 'warehouse label 1', 'status' => 1 ),
		'unit_warehouse2' => array( 'code' => 'unit_warehouse2', 'label' => 'warehouse label 2', 'status' => 1 ),
		'unit_warehouse3' => array( 'code' => 'unit_warehouse3', 'label' => 'warehouse label 3', 'status' => 1 ),
		'unit_warehouse4' => array( 'code' => 'unit_warehouse4', 'label' => 'warehouse label 4', 'status' => 1 ),
		'unit_warehouse5' => array( 'code' => 'unit_warehouse5', 'label' => 'warehouse label 5', 'status' => 1 ),
		'default' => array( 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'product/stock' => array(
		array( 'prodid' => 'product/CNE', 'warehouseid' => 'default', 'stocklevel' => 1000, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'prodid' => 'product/CNC', 'warehouseid' => 'default', 'stocklevel' => 1200, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'prodid' => 'product/U:MD', 'warehouseid' => 'unit_warehouse3', 'stocklevel' => 200, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'prodid' => 'product/U:SD', 'warehouseid' => 'unit_warehouse4', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:PD', 'warehouseid' => 'unit_warehouse5', 'stocklevel' => 2000, 'backdate' => null ),
		array( 'prodid' => 'product/ABCD', 'warehouseid' => 'unit_warehouse1', 'stocklevel' => 1100, 'backdate' => '2010-04-01 00:00:00' ),
		array( 'prodid' => 'product/EFGH', 'warehouseid' => 'unit_warehouse2', 'stocklevel' => 0, 'backdate' => '2015-05-01 00:00:00' ),
		array( 'prodid' => 'product/IJKL', 'warehouseid' => 'unit_warehouse3', 'stocklevel' => 3, 'backdate' => '2006-06-01 00:00:00' ),
		array( 'prodid' => 'product/MNOP', 'warehouseid' => 'unit_warehouse4', 'stocklevel' => null, 'backdate' => null ),
		array( 'prodid' => 'product/U:TESTP', 'warehouseid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:TESTPSUB01', 'warehouseid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:TESTSUB02', 'warehouseid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:TESTSUB03', 'warehouseid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:TESTSUB05', 'warehouseid' => 'default', 'stocklevel' => 100, 'backdate' => null ),
		array( 'prodid' => 'product/U:BUNDLE', 'warehouseid' => 'default', 'stocklevel' => 1000, 'backdate' => null ),
	)
);