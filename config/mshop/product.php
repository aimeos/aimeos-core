<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'property' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_product_property"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_product_property" ( :names
						"parentid", "key", "type", "langid", "value",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_product_property"
					SET :names
						"parentid" = ?, "key" = ?, "type" = ?, "langid" = ?,
						"value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_product_property" mpropr
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_product_property" mpropr
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpropr."id"
						FROM "mshop_product_property" mpropr
						:joins
						WHERE :cond
						ORDER BY mpropr."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mpropr."id"
						FROM "mshop_product_property" mpropr
						:joins
						WHERE :cond
						ORDER BY mpropr."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_product_property_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_product"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_product" ( :names
					"type", "code", "dataset", "label", "url", "instock", "status", "scale",
					"start", "end", "config", "target", "boost", "editor", "mtime", "ctime", "siteid"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_product"
				SET :names
					"type" = ?, "code" = ?, "dataset" = ?, "label" = ?, "url" = ?, "instock" = ?,
					"status" = ?, "scale" = ?, "start" = ?, "end" = ?, "config" = ?, "target" = ?,
					"boost" = ?, "editor" = ?, "mtime" = ?, "ctime" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
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
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_product" mpro
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_product" mpro
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
					SELECT mpro."id"
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT mpro."id"
					FROM "mshop_product" mpro
					:joins
					WHERE :cond
					GROUP BY mpro."id"
					ORDER BY mpro."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_product_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
