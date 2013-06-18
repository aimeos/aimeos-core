<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'customer/list/type' => array (
		'text/default' => array( 'domain' => 'text', 'code' => 'default', 'label' => 'Default', 'status' => 1 ),
	),

	'customer/list' => array (
		array( 'parentid' => 'customer/UTC003', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/information', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'pos' => 0 ),
		array( 'parentid' => 'customer/UTC003', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/notify', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'pos' => 1 ),
		array( 'parentid' => 'customer/UTC003', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/newsletter', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'pos' => 2 ),
		array( 'parentid' => 'customer/UTC001', 'typeid' => 'text/default', 'domain' => 'text', 'refid' => 'text/customer/information', 'start' => '2010-01-01 00:00:00', 'end' => '2022-01-01 00:00:00', 'pos' => 2 ),
	),
);