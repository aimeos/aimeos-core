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
