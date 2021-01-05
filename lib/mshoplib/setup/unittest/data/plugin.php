<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

return [
	'plugin/type' => [
		['plugin.type.domain' => 'plugin', 'plugin.type.code' => 'order', 'plugin.type.label' => 'Order', 'plugin.type.status' => 1],
	],

	'plugin' => [[
		'plugin.type' => 'order', 'plugin.label' => 'Shipping-Plugin', 'plugin.provider' => 'Shipping,Example',
		'plugin.config' => ["threshold" => ["EUR" =>"34.00"]], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'ProductLimit-Plugin', 'plugin.provider' => 'ProductLimit,Example',
		'plugin.config' => ["single-number-max" => "10"], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'BasketLimits-Plugin', 'plugin.provider' => 'BasketLimits,Example',
		'plugin.config' => ["min-value" => ["EUR" => "31.00"]], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'ServicesAvailable-Plugin', 'plugin.provider' => 'ServicesAvailable,Example',
		'plugin.config' => ["payment" => true, "delivery" => true], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'AddressesAvailable-Plugin', 'plugin.provider' => 'AddressesAvailable,Example',
		'plugin.config' => ["payment" => true, "delivery" => null], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'ProductPrice-Plugin', 'plugin.provider' => 'ProductPrice,Example',
		'plugin.config' => ["update" => false], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'ProductStock-Plugin', 'plugin.provider' => 'ProductStock,Example',
		'plugin.config' => [], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'ProductGone-Plugin', 'plugin.provider' => 'ProductGone,Example',
		'plugin.config' => [], 'plugin.status' => 1
	], [
		'plugin.type' => 'order', 'plugin.label' => 'Coupon-Plugin', 'plugin.provider' => 'Coupon,Example',
		'plugin.config' => [], 'plugin.status' => 1
	]],
];
