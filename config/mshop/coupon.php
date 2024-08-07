<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2024
 */


return [
	'manager' => [
		'code' => [
			'counter' => [
				'ansi' => '
					UPDATE "mshop_coupon_code"
					SET	"count" = "count" + ?, "mtime" = ?, "editor" = ?
					WHERE :cond AND "code" = ?
				'
			],
		],
		'submanagers' => [
			'code' => 'code'
		]
	],
];
