<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'plugin/type' => array(
		'plugin/order' => array( 'domain' => 'plugin', 'code' => 'order', 'label' => 'Order', 'status' => 1 )
	),

	'plugin' => array(
		'plugin/order/Shipping-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'Shipping-Plugin', 'provider' => 'Shipping,Example', 'config' => array( "threshold" => array( "EUR" =>"34.00" ) ), 'status' => 1 ),
		'plugin/order/ProductLimit-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'ProductLimit-Plugin', 'provider' => 'ProductLimit,Example', 'config' => array( "single-number-max" => "10" ), 'status' => 1 ),
		'plugin/order/BasketLimits-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'BasketLimits-Plugin', 'provider' => 'BasketLimits,Example', 'config' => array( "min-value" => array( "EUR" => "31.00" ) ), 'status' => 1 ),
		'plugin/order/ServicesAvailable-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'ServicesAvailable-Plugin', 'provider' => 'ServicesAvailable,Example', 'config' => array( "payment" => true, "delivery" => true ), 'status' => 1 ),
		'plugin/order/AddressesAvailable-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'AddressesAvailable-Plugin', 'provider' => 'AddressesAvailable,Example', 'config' => array( "payment" => true, "delivery" => null ), 'status' => 1 ),
		'plugin/order/ProductPrice-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'ProductPrice-Plugin', 'provider' => 'ProductPrice,Example', 'config' => array( "update" => false ), 'status' => 1 ),
		'plugin/order/ProductStock-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'ProductStock-Plugin', 'provider' => 'ProductStock,Example', 'config' => [], 'status' => 1 ),
		'plugin/order/ProductGone-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'ProductGone-Plugin', 'provider' => 'ProductGone,Example', 'config' => [], 'status' => 1 ),
		'plugin/order/Coupon-Plugin' => array( 'typeid' => 'plugin/order', 'label' => 'Coupon-Plugin', 'provider' => 'Coupon,Example', 'config' => [], 'status' => 1 ),
	)
);
