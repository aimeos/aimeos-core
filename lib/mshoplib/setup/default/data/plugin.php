<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

return [
	'plugin' => [
		['type' => 'order', 'label' => 'Updates delivery/payment options on basket change', 'provider' => 'ServicesUpdate', 'config' => [], 'position' => 0, 'status' => 1],
		['type' => 'order', 'label' => 'Adds addresses/delivery/payment to basket', 'provider' => 'Autofill', 'config' => ['address' => 1, 'useorder' => 1, 'orderaddress' => 1, 'orderservice' => 1, 'delivery' => 1, 'payment' => 0], 'position' => 1, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for required addresses (billing/delivery)', 'provider' => 'AddressesAvailable', 'config' => ['payment' => 1, 'delivery' => ''], 'position' => 2, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for required services (delivery/payment)', 'provider' => 'ServicesAvailable', 'config' => ['payment' => 1, 'delivery' => 1], 'position' => 3, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for deleted products', 'provider' => 'ProductGone', 'config' => [], 'position' => 4, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for changed product prices', 'provider' => 'ProductPrice', 'config' => [], 'position' => 5, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for necessary basket limits', 'provider' => 'BasketLimits', 'config' => ['min-products' => 1, 'max-products' => 100, 'min-value' => ['EUR' => '1.00'], 'max-value' => ['EUR' => '10000.00']], 'position' => 6, 'status' => 0],
		['type' => 'order', 'label' => 'Limits maximum amount of products', 'provider' => 'ProductLimit', 'config' => ['single-number-max' => 10, 'total-number-max' => 100, 'single-value-max' => ['EUR' => '1000.00'], 'total-value-max' => ['EUR' => '10000.00']], 'position' => 7, 'status' => 0],
		['type' => 'order', 'label' => 'Free shipping above threshold', 'provider' => 'Shipping', 'config' => ['threshold' => ['EUR' => '1.00']], 'position' => 8, 'status' => 0],
		['type' => 'order', 'label' => 'Country and state tax rates', 'provider' => 'Taxrate', 'config' => ['country-taxrates' => ['US' => '5.00', 'AT' => '20.00'], 'state-taxrates' => ['CA' =>'6.25']], 'position' => 9, 'status' => 0],
		['type' => 'order', 'label' => 'Coupon update', 'provider' => 'Coupon', 'config' => [], 'position' => 100, 'status' => 1],
		['type' => 'order', 'label' => 'Checks for products out of stock', 'provider' => 'ProductStock', 'config' => [], 'position' => 101, 'status' => 1],
	]
];
