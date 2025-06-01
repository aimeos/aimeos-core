<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2025
 */


return [
	'manager' => [
		'decorators' => [
			'global' => [
				'Lists' => 'Lists',
				'Property' => 'Property',
				'Type' => 'Type',
				'Site' => 'Site',
			],
			'local' => [
				'Stock' => 'Stock',
			]
		],
		'lists' => [
			'submanagers' => [
				'type' => 'type',
			]
		],
		'property' => [
			'decorators' => [
				'global' => [
					'Type' => 'Type',
				]
			],
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
		'update' => [
			'ansi' => '
				UPDATE ":table"
				SET :names "ctime" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		],
	],
];
