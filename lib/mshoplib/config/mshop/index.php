<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


return array(
	'manager' => array(
		'attribute' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_index_attribute"
						WHERE :cond AND "siteid" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_index_attribute" (
							"prodid", "attrid", "listtype", "type", "code",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?
						)
					',
					'pgsql' => '
						INSERT INTO "mshop_index_attribute" (
							"prodid", "attrid", "listtype", "type", "code",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?
						)
						ON CONFLICT DO NOTHING
					',
				),
				'search' => array(
					'ansi' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							LIMIT 1000 OFFSET 0
						) AS list
					'
				),
				'cleanup' => array(
					'ansi' => '
						DELETE FROM "mshop_index_attribute"
						WHERE "mtime" < ? AND "siteid" = ?
					'
				),
				'optimize' => array(
					'mysql' => array(
						'OPTIMIZE TABLE "mshop_index_attribute"',
					),
					'pgsql' => [],
					'sqlsrv' => [],
				),
			),
		),
		'catalog' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_index_catalog"
						WHERE :cond AND "siteid" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_index_catalog" (
							"prodid", "catid", "listtype", "pos",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?
						)
					',
					'pgsql' => '
						INSERT INTO "mshop_index_catalog" (
							"prodid", "catid", "listtype", "pos",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?
						)
						ON CONFLICT DO NOTHING
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							LIMIT 1000 OFFSET 0
						) AS list
					'
				),
				'cleanup' => array(
					'ansi' => '
						DELETE FROM "mshop_index_catalog"
						WHERE "mtime" < ? AND "siteid" = ?
					'
				),
				'optimize' => array(
					'mysql' => array(
						'OPTIMIZE TABLE "mshop_index_catalog"',
					),
					'pgsql' => [],
					'sqlsrv' => [],
				),
			),
		),
		'price' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_index_price"
						WHERE :cond AND "siteid" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_index_price" (
							"prodid", "currencyid", "value", "mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?
						)
					',
					'pgsql' => '
						INSERT INTO "mshop_index_price" (
							"prodid", "currencyid", "value", "mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?
						)
						ON CONFLICT DO NOTHING
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							LIMIT 1000 OFFSET 0
						) AS list
					'
				),
				'cleanup' => array(
					'ansi' => '
						DELETE FROM "mshop_index_price"
						WHERE "mtime" < ? AND "siteid" = ?
					'
				),
				'optimize' => array(
					'mysql' => array(
						'OPTIMIZE TABLE "mshop_index_price"',
					),
					'pgsql' => [],
					'sqlsrv' => [],
				),
			),
		),
		'supplier' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_index_supplier"
						WHERE :cond AND "siteid" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_index_supplier" (
							"prodid", "supid", "listtype", "pos",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?
						)
					',
					'pgsql' => '
						INSERT INTO "mshop_index_supplier" (
							"prodid", "supid", "listtype", "pos",
							"mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?
						)
						ON CONFLICT DO NOTHING
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							LIMIT 1000 OFFSET 0
						) AS list
					'
				),
				'cleanup' => array(
					'ansi' => '
						DELETE FROM "mshop_index_supplier"
						WHERE "mtime" < ? AND "siteid" = ?
					'
				),
				'optimize' => array(
					'mysql' => array(
						'OPTIMIZE TABLE "mshop_index_supplier"',
					),
					'pgsql' => [],
					'sqlsrv' => [],
				),
			),
		),
		'text' => array(
			'standard' => array(
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_index_text"
						WHERE :cond AND "siteid" = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_index_text" (
							"prodid", "langid", "url", "name", "content", "mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?
						)
					',
					'pgsql' => '
						INSERT INTO "mshop_index_text" (
							"prodid", "langid", "url", "name", "content", "mtime", "siteid"
						) VALUES (
							?, ?, ?, ?, ?, ?, ?
						)
						ON CONFLICT DO NOTHING
					'
				),
				'search' => array(
					'ansi' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT mpro."id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mpro."id"
							FROM "mshop_product" AS mpro
							:joins
							WHERE :cond
							GROUP BY mpro."id"
							ORDER BY mpro."id"
							LIMIT 1000 OFFSET 0
						) AS list
					'
				),
				'cleanup' => array(
					'ansi' => '
						DELETE FROM "mshop_index_text"
						WHERE "mtime" < ? AND "siteid" = ?
					'
				),
				'optimize' => array(
					'mysql' => array(
						'OPTIMIZE TABLE "mshop_index_text"',
					),
					'pgsql' => [],
					'sqlsrv' => [],
				),
			),
		),
		'standard' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT "key", COUNT("id") AS "count"
					FROM (
						SELECT :key AS "key", mpro."id" AS "id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY :key, mpro."id"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY "key"
				',
				'mysql' => '
					SELECT "key", COUNT("id") AS "count"
					FROM (
						SELECT :key AS "key", mpro."id" AS "id" :mincols
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY :key, mpro."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY "key"
				'
			),
			'search' => array(
				'ansi' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" AS mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT mpro."id" :mincols
					FROM "mshop_product" AS mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						OFFSET 0 ROWS FETCH NEXT 1000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpro."id"
						FROM "mshop_product" AS mpro
						:joins
						WHERE :cond
						GROUP BY mpro."id"
						ORDER BY mpro."id"
						LIMIT 1000 OFFSET 0
					) AS list
				'
			),
			'optimize' => array(
				'mysql' => array(
					'ANALYZE TABLE "mshop_product"',
					'ANALYZE TABLE "mshop_product_list"',
				),
				'pgsql' => [],
				'sqlsrv' => [],
			),
			'domains' => [
				'attribute' => 'attribute',
				'product' => ['default'],
				'price' => ['default'],
				'text' => 'text',
			],
		),
		'submanagers' => [
			'attribute' => 'attribute',
			'supplier' => 'supplier',
			'catalog' => 'catalog',
			'price' => 'price',
			'text' => 'text',
		],
	),
);
