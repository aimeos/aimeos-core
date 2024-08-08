<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
 */


return array(
	'manager' => array(
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" msub
					:joins
					WHERE :cond
					GROUP BY msub.id, :cols, :val
					ORDER BY msub.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_subscription" msub
					:joins
					WHERE :cond
					GROUP BY msub.id, :cols, :val
					ORDER BY msub.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_subscription" msub
				JOIN "mshop_order" mord ON msub."orderid" = mord."id"
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_subscription" msub
				JOIN "mshop_order" mord ON msub."orderid" = mord."id"
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msub."id"
					FROM "mshop_subscription" msub
					JOIN "mshop_order" mord ON msub."orderid" = mord."id"
					:joins
					WHERE :cond
					GROUP BY msub."id"
					ORDER BY msub."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msub."id"
					FROM "mshop_subscription" msub
					JOIN "mshop_order" mord ON msub."orderid" = mord."id"
					:joins
					WHERE :cond
					GROUP BY msub."id"
					ORDER BY msub."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
	),
);
