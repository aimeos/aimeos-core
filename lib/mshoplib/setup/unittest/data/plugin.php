<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: plugin.php 1197 2012-09-13 14:23:18Z gbang $
 */

return array (
	'plugin/type' => array (
		'plugin/order' => array ( 'domain' => 'plugin', 'code' => 'order', 'label' => 'Order', 'status' => 1 )
	),

	'plugin' => array (
		'plugin/order/Shipping-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'Shipping-Plugin', 'provider' => 'Shipping,Example', 'config' => array( "threshold" => array ("EUR" =>"34.00" ) ), 'status' => 1 ),
		'plugin/order/ProductLimit-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'ProductLimit-Plugin', 'provider' => 'ProductLimit,Example', 'config' => array("single-number-max" => "10"), 'status' => 1 ),
		'plugin/order/BasketLimits-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'BasketLimits-Plugin', 'provider' => 'BasketLimits,Example', 'config' => array("minorder" => "31.00"), 'status' => 1 ),
		'plugin/order/ServicesAvailable-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'ServicesAvailable-Plugin', 'provider' => 'ServicesAvailable,Example', 'config' => array("payment" => true, "delivery" => true ), 'status' => 1 ),
		'plugin/order/AddressesAvailable-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'AddressesAvailable-Plugin', 'provider' => 'AddressesAvailable,Example', 'config' => array("payment" => true, "delivery" => null ), 'status' => 1 ),
		'plugin/order/ProductPrice-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'ProductPrice-Plugin', 'provider' => 'ProductPrice,Example', 'config' => array( "update" => false ), 'status' => 1 ),
		'plugin/order/ProductStock-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'ProductStock-Plugin', 'provider' => 'ProductStock,Example', 'config' => array() , 'status' => 1 ),
		'plugin/order/ProductGone-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'ProductGone-Plugin', 'provider' => 'ProductGone,Example', 'config' => array() , 'status' => 1 ),
		'plugin/order/IntelligentSampling-Plugin' => array ( 'typeid' => 'plugin/order', 'label' => 'IntelligentSampling-Plugin', 'provider' => 'IntelligentSampling,Example', 'config' => array( "samples" => array( "CNE" => array ("conditions" => array ("&&" => array( array("!=" => array("exists(\"U:MD\")" => true)), array("!=" => array("exists(\"U:SD\")" => true))))), "CNC" => array ("conditions" => array ("&&" => array ( array( "!=" => array ("exists(\"U:MD\")" => true)), array ("!=" => array ( "exists(\"U:SD\")" => true)), array ("!=" => array ("exists(\"U:PD\")" => true)))))), "alternative" => "CNE", "firsttime" => 1 ), 'status' => 1),
	)
);
