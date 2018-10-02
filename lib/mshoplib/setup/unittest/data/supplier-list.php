<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

return array (
	'supplier/lists/type' => array (
		'attribute/default' => array( 'domain' => 'attribute', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'media/default' => array( 'domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'product/default' => array( 'domain' => 'product', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'supplier/lists' => array (
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'product/default', 'domain' => 'product', 'refid' => 'product/CNC', 'start' => '2010-01-01 00:00:00', 'end' => '2100-01-01 00:00:00', 'config' => [], 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'product/default', 'domain' => 'product', 'refid' => 'product/CNE', 'start' => '2010-01-01 00:00:00', 'end' => null, 'config' => [], 'pos' => 2, 'status' => 1 ),

		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/supplier/description', 'start' => '2010-01-01 00:00:00', 'end' => '2100-01-01 00:00:00', 'config' => [], 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/notify', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => [], 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/newsletter', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => [], 'pos' => 3, 'status' => 1 ),

		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'media/default', 'domain' => 'media', 'refid' => 'media/path/to/supplier.jpg', 'start' => null, 'end' => null, 'config' => [], 'pos' => 0, 'status' => 1 ),
	),
);