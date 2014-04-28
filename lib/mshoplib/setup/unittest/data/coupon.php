<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @version $Id: coupon.php 180 2012-11-08 17:21:23Z doleiynyk $
 */

return array (

	'coupon' => array (
		'FixedRebate/1' => array ( 'label' => 'Unit test fixed rebate', 'provider' => 'FixedRebate', 'status' => 1, 'config' => array( 'product' => 'U:MD', 'kraft-reasoncode' => '0B', 'minorder' => '9.00', 'rebate' => '2.50' ), 'start' => '2002-01-01 00:00:00', 'end' => '2100-12-31 00:00:00' ),
		'PercentRebate/1' => array ( 'label' => 'Unit test percent rebate', 'provider' => 'PercentRebate', 'status' => 1, 'config' => array( 'kraft-reasoncode' => '0C', 'minorder' => '9.00', 'product' => 'U:MD', 'rebate' => '10' ), 'start' => null, 'end' => null ),
		'FreeShipping/1' => array ( 'label' => 'Unit test free shipping', 'provider' => 'FreeShipping', 'status' => 1, 'config' => array( 'kraft-reasoncode' => 'FC', 'minorder' => '9.00', 'product' => 'U:SD' ), 'start' => null, 'end' => null ),
		'Present/1' => array ( 'label' => 'Unit test present', 'provider' => 'Present', 'status' => 1, 'config' => array( 'kraft-reasoncode' => '0E', 'minorder' => '9.00', 'product' => 'U:PD', 'quantity' => 2 ), 'start' => null, 'end' => null ),
		'Example/1' => array ( 'label' => 'Unit test example', 'provider' => 'Example', 'status' => 1, 'config' => array( 'minorder' => '9.00' ), 'start' => null, 'end' => null ),
	),

	'coupon/code' => array(
		'5678' => array ( 'couponid' => 'FixedRebate/1', 'code' => '5678', 'count' => 2000000, 'start' => '2000-01-01 00:00:00', 'end' => '2004-12-21 23:59:59' ),
		'90AB' => array ( 'couponid' => 'PercentRebate/1', 'code' => '90AB', 'count' => 2000000, 'start' => null, 'end' => null ),
		'CDEF' => array ( 'couponid' => 'FreeShipping/1', 'code' => 'CDEF', 'count' => 2000000, 'start' => null, 'end' => null ),
		'GHIJ' => array ( 'couponid' => 'Present/1', 'code' => 'GHIJ', 'count' => 2000000, 'start' => null, 'end' => null ),
		'OPQR' => array ( 'couponid' => 'Example/1', 'code' => 'OPQR', 'count' => 2000000, 'start' => null, 'end' => null ),
	),

	//ordprodid => prodcode/quantity/pos
	'order/base/coupon' => array(
		array( 'baseid' => '53.50', 'ordprodid' => 'U:MD/1/3', 'code' => '5678' ),
		array( 'baseid' => '53.50', 'ordprodid' => 'ABCD/1/4', 'code' => 'OPQR' ),
		array( 'baseid' => '672.00', 'ordprodid' => 'CNE/2/1', 'code' => '5678' ),
		array( 'baseid' => '672.00', 'ordprodid' => 'CNC/1/2', 'code' => 'OPQR' ),
	)
);