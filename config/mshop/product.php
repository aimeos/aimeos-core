<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'submanagers' => [
			'lists' => 'lists',
			'property' => 'property',
		],
		'rate' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET "rating" = ?, "ratings" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'stock' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET "instock" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
	),
);
