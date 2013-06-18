<?php
/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'price/list/type' => array(
		'customer/default' => array( 'domain' => 'customer', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'price/list' => array(
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC001', 'start' => null, 'end' => null, 'pos' => 0 ),
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC002', 'start' => null, 'end' => null, 'pos' => 1 ),
		array( 'parentid' => 'price/attribute/default/99.99/9.99', 'typeid' => 'customer/default', 'domain' => 'customer', 'refid' => 'customer/UTC003', 'start' => '2002-01-01 00:00:00', 'end' => '2006-12-31 23:59:59', 'pos' => 2 ),
	)
);