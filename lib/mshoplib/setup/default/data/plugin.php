<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'plugin' => [[
		'type' => 'order', 'label' => 'Limits maximum amount of products',
		'provider' => 'ProductLimit', 'position' => 10, 'status' => 0,
		'config' => [
			'single-number-max' => 10,
			'total-number-max' => 100,
			'single-value-max' => ['EUR' => '1000.00'],
			'total-value-max' => ['EUR' => '10000.00']
		]
	], [
		'type' => 'order', 'label' => 'Checks for deleted products',
		'provider' => 'ProductGone', 'position' => 20, 'status' => 1,
		'config' => []
	], [
		'type' => 'order', 'label' => 'Checks for products out of stock',
		'provider' => 'ProductStock', 'position' => 30, 'status' => 1,
		'config' => []
	], [
		'type' => 'order', 'label' => 'Checks for changed product prices',
		'provider' => 'ProductPrice', 'position' => 40, 'status' => 1,
		'config' => []
	], [
		'type' => 'order', 'label' => 'Adds addresses/delivery/payment to basket',
		'provider' => 'Autofill', 'position' => 50, 'status' => 1,
		'config' => [
			'address' => 1,
			'useorder' => 1,
			'orderaddress' => 1,
			'orderservice' => 1,
			'delivery' => 1,
			'payment' => 0
		]
	], [
		'type' => 'order', 'label' => 'Updates delivery/payment options on basket change',
		'provider' => 'ServicesUpdate', 'position' => 60, 'status' => 1,
		'config' => []
	], [
		'type' => 'order', 'label' => 'Free shipping above threshold',
		'provider' => 'Shipping', 'position' => 70, 'status' => 0,
		'config' => [
			'threshold' => ['EUR' => '1.00']
		]
	], [
		'type' => 'order', 'label' => 'Checks for necessary basket limits',
		'provider' => 'BasketLimits', 'position' => 80, 'status' => 0,
		'config' => [
			'min-products' => 1,
			'max-products' => 100,
			'min-value' => ['EUR' => '1.00'],
			'max-value' => ['EUR' => '10000.00']
		]
	], [
		'type' => 'order', 'label' => 'Country and state tax rates',
		'provider' => 'Taxrates', 'position' => 90, 'status' => 0,
		'config' => [
			'country-taxrates' => ['US' => '5.00', 'AT' => '20.00'],
			'state-taxrates' => ['CA' =>'6.25']
		]
	], [
		'type' => 'order', 'label' => 'Coupon update',
		'provider' => 'Coupon', 'position' => 100, 'status' => 1,
		'config' => []
	], [
		'type' => 'order', 'label' => 'Checks for required addresses (billing/delivery)',
		'provider' => 'AddressesAvailable', 'position' => 110, 'status' => 1,
		'config' => [
			'payment' => 1,
			'delivery' => ''
		]
	], [
		'type' => 'order', 'label' => 'Checks for required services (delivery/payment)',
		'provider' => 'ServicesAvailable', 'position' => 120, 'status' => 1,
		'config' => [
			'payment' => 1,
			'delivery' => 1
		]
	]]
];
