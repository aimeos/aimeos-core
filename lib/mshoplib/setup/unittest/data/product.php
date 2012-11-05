<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: product.php 1182 2012-08-30 14:40:13Z gwussow $
 */

return array (
	'product/type' => array (
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Article', 'status' => 1 ),
		'product/select' => array( 'domain' => 'product', 'code' => 'select', 'label' => 'Selection', 'status' => 1 ),
		'product/rebate' => array( 'domain' => 'product', 'code' => 'rebate', 'label' => 'Rebate', 'status' => 1 ),
		'product/bundle' => array( 'domain' => 'product', 'code' => 'bundle', 'label' => 'Bundle', 'status' => 1 ),
	),

	'product' => array (
		'product/CNE' => array( 'typeid' => 'product/default', 'label' => 'Cafe Noire Expresso', 'code' => 'CNE', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/CNC' => array( 'typeid' => 'product/default', 'label' => 'Cafe Noire Cappuccino', 'code' => 'CNC', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:MD' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Monetary rebate', 'code' => 'U:MD', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/U:SD' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Shipping rebate', 'code' => 'U:SD', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/U:PD' => array( 'typeid' => 'product/rebate', 'label' => 'Unittest: Present rebate', 'code' => 'U:PD', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/U:WH' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Present rebate', 'code' => 'U:WH', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/ABCD' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'ABCD', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/EFGH' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'EFGH', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/IJKL' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'IJKL', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/MNOP' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'MNOP', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/QRST' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'QRST', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/U:CF' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Cheapest free rebate', 'code' => 'U:CF', 'suppliercode' => 'unitSupplier', 'status' => 0 ),
		'product/U:TEST' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Test Selection', 'code' => 'U:TEST', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTSUB01' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 1', 'code' => 'U:TESTSUB01', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTSUB02' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 2', 'code' => 'U:TESTSUB02', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTSUB03' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 3', 'code' => 'U:TESTSUB03', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTSUB04' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 4', 'code' => 'U:TESTSUB04', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTSUB05' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 5', 'code' => 'U:TESTSUB05', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:noSel' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Empty Selection', 'code' => 'U:noSel', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTP' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Test priced Selection', 'code' => 'U:TESTP', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
		'product/U:TESTPSUB01' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test priced Sub 1', 'code' => 'U:TESTPSUB01', 'suppliercode' => 'unitSupplier', 'status' => 1 ),
	)
);