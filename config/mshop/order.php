<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'address' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_address" mordad
						:joins
						WHERE :cond
						GROUP BY mordad."id", :cols
						ORDER BY mordad."id" DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_address" mordad
						:joins
						WHERE :cond
						GROUP BY mordad."id", :cols
						ORDER BY mordad."id" DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
		),
		'coupon' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_coupon" mordco
						:joins
						WHERE :cond
						GROUP BY mordco."id", :cols
						ORDER BY mordco."id" DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_coupon" mordco
						:joins
						WHERE :cond
						GROUP BY mordco."id", :cols
						ORDER BY mordco."id" DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
		),
		'product' => array(
			'submanagers' => [
				'attribute' => 'attribute'
			],
			'attribute' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_product_attr" mordprat
							:joins
							WHERE :cond
							GROUP BY mordprat."id", :cols
							ORDER BY mordprat."id" DESC
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_product_attr" mordprat
							:joins
							WHERE :cond
							GROUP BY mordprat."id", :cols
							ORDER BY mordprat."id" DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_product" mordpr
						:joins
						WHERE :cond
						GROUP BY mordpr."id", :cols
						ORDER BY mordpr."id" DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_product" mordpr
						:joins
						WHERE :cond
						GROUP BY mordpr."id", :cols
						ORDER BY mordpr."id" DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_product" ( :names
						"currencyid", "price", "costs", "rebate", "tax", "taxrate", "taxflag",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_product"
					SET :names
						"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?, "taxflag" = ?,
						"mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
		),
		'service' => array(
			'submanagers' => [
				'attribute' => 'attribute',
				'transaction' => 'transaction',
			],
			'attribute' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_service_attr" mordseat
							:joins
							WHERE :cond
							GROUP BY mordseat."id", :cols
							ORDER BY mordseat."id" DESC
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_service_attr" mordseat
							:joins
							WHERE :cond
							GROUP BY mordseat."id", :cols
							ORDER BY mordseat."id" DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
			),
			'transaction' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_service_tx" mordsetx
							:joins
							WHERE :cond
							GROUP BY mordsetx."id", :cols
							ORDER BY mordsetx."id" DESC
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_service_tx" mordsetx
							:joins
							WHERE :cond
							GROUP BY mordsetx."id", :cols
							ORDER BY mordsetx."id" DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_service_tx" ( :names
							"currencyid", "price", "costs", "rebate", "tax", "taxflag",
							"mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_service_tx"
						SET :names
							"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxflag" = ?,
							"mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
			),
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_service" mordse
						:joins
						WHERE :cond
						GROUP BY mordse."id", :cols
						ORDER BY mordse."id" DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_service" mordse
						:joins
						WHERE :cond
						GROUP BY mordse."id", :cols
						ORDER BY mordse."id" DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_service" ( :names
						"currencyid", "price", "costs", "rebate", "tax", "taxrate", "taxflag",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_service"
					SET :names
						"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?, "taxflag" = ?,
						"mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
		),
		'basket' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_basket"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'mysql' => '
					INSERT INTO "mshop_order_basket" ( :names
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?
					) ON DUPLICATE KEY UPDATE
						"customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
				',
				'pgsql' => '
					INSERT INTO "mshop_order_basket" ( :names
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?
					) ON CONFLICT ("id") DO UPDATE SET
						"customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
				',
				'sqlsrv' => '
					MERGE "mshop_order_basket" AS tgt
					USING ( SELECT ?, ?, ?, ?, ?, ?, ?, ? ) AS src (
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) ON (tgt."id" = src."id")
					WHEN MATCHED THEN
						UPDATE SET "customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
					WHEN NOT MATCHED THEN
						INSERT ( :names
							"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
						) VALUES ( :values
							src."customerid", src."content", src."name", src."mtime", src."editor", src."siteid", src."ctime", src."id"
						);
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_basket" mordba
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_basket" mordba
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordba."id" ) AS "count"
					FROM "mshop_order_basket" mordba
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_basket_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'status' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_status" mordst
						:joins
						WHERE :cond
						GROUP BY mordst."id", :cols
						ORDER BY mordst."id" DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_status" mordst
						:joins
						WHERE :cond
						GROUP BY mordst."id", :cols
						ORDER BY mordst."id" DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
		),
		'aggregate' => array(
			'ansi' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :type(:val) AS "val"
					FROM "mshop_order" mord
					:joins
					WHERE :cond
					GROUP BY mord."id", :cols
					ORDER BY mord."id" DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :type(:val) AS "val"
					FROM "mshop_order" mord
					:joins
					WHERE :cond
					GROUP BY mord."id", :cols
					ORDER BY mord."id" DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_order" ( :names
					"sitecode", "langid", "currencyid",
					"price", "costs", "rebate", "tax", "taxflag",
					"cdate", "cmonth", "cweek", "cwday", "chour",
					"mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_order"
				SET :names
					"sitecode" = ?, "langid" = ?, "currencyid" = ?, "price" = ?, "costs" = ?,
					"rebate" = ?, "tax" = ?, "taxflag" = ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'subdomains' => [
			'order/address' => 'order/address',
			'order/coupon' => 'order/coupon',
			'order/product' => 'order/product',
			'order/service' => 'order/service',
			'order/status' => 'order/status',
		],
		'submanagers' => [
			'address' => 'address',
			'coupon' => 'coupon',
			'product' => 'product',
			'service' => 'service',
			'status' => 'status',
		],
	),
);
