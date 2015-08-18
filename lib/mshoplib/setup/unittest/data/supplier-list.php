<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array (
	'supplier/list/type' => array (
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'supplier/list' => array (
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/supplier/description', 'start' => '2010-01-01 00:00:00', 'end' => '2100-01-01 00:00:00', 'config' => array(), 'pos' => 0, 'status' => 1 ),
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/notify', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 1, 'status' => 1 ),
		array( 'parentid' => 'supplier/unitCode001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/newsletter', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'config' => array(), 'pos' => 2, 'status' => 1 ),
	),
);