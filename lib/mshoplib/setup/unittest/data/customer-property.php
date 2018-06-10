<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

return array (
	'customer/property/type' => array(
		'customer/property/type/newsletter' => array( 'domain' => 'customer', 'code' => 'newsletter', 'label' => 'Newsletter', 'status' => 1 ),
	),

	'customer/property' => array(
		'customer/property/UTC001/newsletter' => array( 'parentid' => 'customer/UTC001', 'typeid' => 'customer/property/type/newsletter', 'langid' => null, 'value' => '1' ),
	),
);