<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(

	'coupon' => array(
		'FixedRebate/1' => array( 'label' => 'Unit test fixed rebate', 'provider' => 'FixedRebate,BasketValues', 'status' => 1, 'config' => array( 'fixedrebate.productcode' => 'U:MD', 'basketvalues.total-value-min' => array( 'EUR' => '9.00' ), 'fixedrebate.rebate' => '2.50' ), 'start' => '2002-01-01 00:00:00', 'end' => '2100-12-31 00:00:00' ),
		'PercentRebate/1' => array( 'label' => 'Unit test percent rebate', 'provider' => 'PercentRebate,BasketValues', 'status' => 1, 'config' => array( 'basketvalues.total-value-min' => array( 'EUR' => '9.00' ), 'percentrebate.productcode' => 'U:MD', 'percentrebate.rebate' => '10' ), 'start' => null, 'end' => null ),
		'FreeShipping/1' => array( 'label' => 'Unit test free shipping', 'provider' => 'FreeShipping,BasketValues', 'status' => 1, 'config' => array( 'basketvalues.total-value-min' => array( 'EUR' => '9.00' ), 'freeshipping.productcode' => 'U:SD' ), 'start' => null, 'end' => null ),
		'Present/1' => array( 'label' => 'Unit test present', 'provider' => 'Present,BasketValues', 'status' => 1, 'config' => array( 'basketvalues.total-value-min' => array( 'EUR' => '9.00' ), 'present.productcode' => 'U:PD', 'present.quantity' => 2 ), 'start' => null, 'end' => null ),
		'Example/1' => array( 'label' => 'Unit test example', 'provider' => 'Example,BasketValues', 'status' => 1, 'config' => array( 'basketvalues.total-value-min' => array( 'EUR' => '9.00' ) ), 'start' => null, 'end' => null ),
	),

	'coupon/code' => array(
		'5678' => array( 'parentid' => 'FixedRebate/1', 'code' => '5678', 'count' => 2000000, 'start' => '2000-01-01 00:00:00', 'end' => '2004-12-21 23:59:59' ),
		'90AB' => array( 'parentid' => 'PercentRebate/1', 'code' => '90AB', 'count' => 2000000, 'start' => null, 'end' => null ),
		'CDEF' => array( 'parentid' => 'FreeShipping/1', 'code' => 'CDEF', 'count' => 2000000, 'start' => null, 'end' => null ),
		'GHIJ' => array( 'parentid' => 'Present/1', 'code' => 'GHIJ', 'count' => 2000000, 'start' => null, 'end' => null ),
		'OPQR' => array( 'parentid' => 'Example/1', 'code' => 'OPQR', 'count' => 2000000, 'start' => null, 'end' => null ),
	),

	//ordprodid => prodcode/quantity/pos
	'order/base/coupon' => array(
		array( 'baseid' => '53.50', 'ordprodid' => 'U:MD/1/3', 'code' => '5678' ),
		array( 'baseid' => '53.50', 'ordprodid' => 'ABCD/1/4', 'code' => 'OPQR' ),
		array( 'baseid' => '672.00', 'ordprodid' => 'CNE/2/1', 'code' => '5678' ),
		array( 'baseid' => '672.00', 'ordprodid' => 'CNC/1/2', 'code' => 'OPQR' ),
	)
);