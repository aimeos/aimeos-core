<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'product/type' => array(
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Article', 'status' => 1 ),
		'product/select' => array( 'domain' => 'product', 'code' => 'select', 'label' => 'Selection', 'status' => 1 ),
		'product/rebate' => array( 'domain' => 'product', 'code' => 'rebate', 'label' => 'Rebate', 'status' => 1 ),
		'product/bundle' => array( 'domain' => 'product', 'code' => 'bundle', 'label' => 'Bundle', 'status' => 1 ),
	),

	'product' => array(
		'product/CNE' => array( 'typeid' => 'product/default', 'label' => 'Cafe Noire Expresso', 'code' => 'CNE', 'config' => array( 'css-class' => 'top' ), 'status' => 1 ),
		'product/CNC' => array( 'typeid' => 'product/default', 'label' => 'Cafe Noire Cappuccino', 'code' => 'CNC', 'config' => array( 'css-class' => 'sale' ), 'status' => 1 ),
		'product/U:MD' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Monetary rebate', 'code' => 'U:MD', 'status' => 0 ),
		'product/U:SD' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Shipping rebate', 'code' => 'U:SD', 'status' => 0 ),
		'product/U:PD' => array( 'typeid' => 'product/rebate', 'label' => 'Unittest: Present rebate', 'code' => 'U:PD', 'status' => 0 ),
		'product/U:WH' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Present rebate', 'code' => 'U:WH', 'status' => 0 ),
		'product/ABCD' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'ABCD', 'status' => 1 ),
		'product/EFGH' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'EFGH', 'status' => 1 ),
		'product/IJKL' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'IJKL', 'status' => 1 ),
		'product/MNOP' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'MNOP', 'status' => 1 ),
		'product/QRST' => array( 'typeid' => 'product/default', 'label' => '16 discs', 'code' => 'QRST', 'status' => 0 ),
		'product/U:CF' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Cheapest free rebate', 'code' => 'U:CF', 'status' => 0 ),
		'product/U:TEST' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Test Selection', 'code' => 'U:TEST', 'status' => 1 ),
		'product/U:TESTSUB01' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 1', 'code' => 'U:TESTSUB01', 'status' => 1 ),
		'product/U:TESTSUB02' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 2', 'code' => 'U:TESTSUB02', 'status' => 1 ),
		'product/U:TESTSUB03' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 3', 'code' => 'U:TESTSUB03', 'status' => 1 ),
		'product/U:TESTSUB04' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 4', 'code' => 'U:TESTSUB04', 'status' => 1 ),
		'product/U:TESTSUB05' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test Sub 5', 'code' => 'U:TESTSUB05', 'status' => 1 ),
		'product/U:noSel' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Empty Selection', 'code' => 'U:noSel', 'status' => 1 ),
		'product/U:TESTP' => array( 'typeid' => 'product/select', 'label' => 'Unittest: Test priced Selection', 'code' => 'U:TESTP', 'status' => 1 ),
		'product/U:TESTPSUB01' => array( 'typeid' => 'product/default', 'label' => 'Unittest: Test priced Sub 1', 'code' => 'U:TESTPSUB01', 'status' => 1 ),
		'product/bdl:zyx' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:zyx', 'code' => 'bdl:zyx', 'status' => 1 ),
		'product/bdl:EFG' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:EFG', 'code' => 'bdl:EFG', 'status' => 1 ),
		'product/bdl:HIJ' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:HIJ', 'code' => 'bdl:HIJ', 'status' => 1 ),
		'product/bdl:hal' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:hal', 'code' => 'bdl:hal', 'status' => 1 ),
		'product/bdl:EFX' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:EFX', 'code' => 'bdl:EFX', 'status' => 1 ),
		'product/bdl:HKL' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle bdl:HKL', 'code' => 'bdl:HKL', 'status' => 1 ),
		'product/U:BUNDLE' => array( 'typeid' => 'product/bundle', 'label' => 'Unittest: Bundle', 'code' => 'U:BUNDLE', 'status' => 1 ),
	)
);