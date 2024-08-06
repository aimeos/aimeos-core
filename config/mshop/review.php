<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */


return array(
	'manager' => array(
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY mrev.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'aggregaterate' => array(
			'ansi' => '
				SELECT :keys, SUM("val") AS "sum", COUNT(*) AS "count"
				FROM (
					SELECT :acols, mrev.rating AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, SUM("val") AS "sum", COUNT(*) AS "count"
				FROM (
					SELECT :acols, mrev.rating AS "val"
					FROM "mshop_review" mrev
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
	),
);
