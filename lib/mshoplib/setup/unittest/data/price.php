<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2021
 */

return [
	'price/type' => [
		'attribute/default' => ['price.type.domain' => 'attribute', 'price.type.code' => 'default', 'price.type.label' => 'Standard'],
		'product/default' => ['price.type.domain' => 'product', 'price.type.code' => 'default', 'price.type.label' => 'Standard'],
		'product/purchase' => ['price.type.domain' => 'product', 'price.type.code' => 'purchase', 'price.type.label' => 'Purchase'],
		'service/default' => ['price.type.domain' => 'service', 'price.type.code' => 'default', 'price.type.label' => 'Standard'],
	],

	'price/lists/type' => [
		'customer/test' => ['price.lists.type.domain' => 'customer', 'price.lists.type.code' => 'test', 'price.lists.type.label' => 'Standard'],
		'product/default' => ['price.lists.type.domain' => 'product', 'price.lists.type.code' => 'default', 'price.lists.type.label' => 'Standard'],
	],

	'price/property/type' => [
		'price/zone' => ['price.property.type.domain' => 'price', 'price.property.type.code' => 'zone', 'price.property.type.label' => 'Tax zone'],
	],
];
