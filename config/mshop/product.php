<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */


return [
	'manager' => [
		'decorators' => [
			'global' => [
				'Site' => 'Site',
			]
		],
		'lists' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'property' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'submanagers' => [
			'lists' => 'lists',
			'property' => 'property',
			'type' => 'type',
		],
		'rate' => [
			'ansi' => '
				UPDATE "mshop_product"
				SET "rating" = ?, "ratings" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		],
		'stock' => [
			'ansi' => '
				UPDATE "mshop_product"
				SET "instock" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		],
	],
];
