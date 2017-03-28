<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'price/lists/type' => array(
		'customer/default' => array( 'domain' => 'customer', 'code' => 'default', 'label' => 'Standard', 'status' => 1 ),
	),

	'price/lists' => array(
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC001', 'start' => null, 'end' => null, 'config' => [], 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC002', 'start' => null, 'end' => null, 'config' => [], 'pos' => 2, 'status' => 1 ),
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC003', 'start' => '2002-01-01 00:00:00', 'end' => '2006-12-31 23:59:59', 'config' => [], 'pos' => 3, 'status' => 1 ),
	)
);