<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org], 2015-2026
 */


return [
	'manager' => [
		'code' => [
			'counter' => [
				'ansi' => '
					UPDATE "mshop_coupon_code"
					SET	"count" = "count" + ?, "mtime" = ?, "editor" = ?
					WHERE :cond AND "code" = ? AND ( "count" IS NULL OR "count" + ? >= 0 )
				'
			],
		],
		'submanagers' => [
			'code' => 'code'
		]
	],
];
