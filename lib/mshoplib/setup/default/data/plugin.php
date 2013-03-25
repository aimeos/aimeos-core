<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array (
	'plugin' => array (
		array ( 'typeid' => 'plugin/order', 'label' => 'Free shipping above threshold', 'provider' => 'Shipping', 'config' => array( "threshold" => array ("EUR" =>"1.00" ) ), 'status' => 0 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Limits maximum amount of products', 'provider' => 'ProductLimit', 'config' => array("single-number-max" => "10"), 'status' => 0 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for necessary basket limits', 'provider' => 'BasketLimits', 'config' => array("minorder" => "1.00"), 'status' => 0 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for required services (delivery/payment)', 'provider' => 'ServicesAvailable', 'config' => array("payment" => true, "delivery" => true ), 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for required addresses', 'provider' => 'AddressesAvailable', 'config' => array("payment" => true, "delivery" => null ), 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for changed product prices', 'provider' => 'ProductPrice', 'config' => array( "update" => false ), 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for products out of stock', 'provider' => 'ProductStock', 'config' => array() , 'status' => 1 ),
		array ( 'typeid' => 'plugin/order', 'label' => 'Checks for deleted products', 'provider' => 'ProductGone', 'config' => array() , 'status' => 1 ),
	)
);