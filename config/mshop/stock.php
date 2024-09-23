<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */


return [
	'manager' => [
		'decorators' => [
			'global' => [
				'Type' => 'Type',
			]
		],
		'submanagers' => [
			'type' => 'type',
		],
		'stocklevel' => [
			'ansi' => '
				UPDATE "mshop_stock"
				SET "stocklevel" = "stocklevel" - ?, "mtime" = ?, "editor" = ?
				WHERE "prodid" = ? AND "type" = ? AND :cond
			'
		],
	],
];
