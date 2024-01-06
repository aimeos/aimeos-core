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
						GROUP BY mordad.id, :cols
						ORDER BY mordad.id DESC
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
						GROUP BY mordad.id, :cols
						ORDER BY mordad.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_address"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_address" ( :names
						"parentid", "addrid", "type", "company", "vatid", "salutation",
						"title", "firstname", "lastname", "address1", "address2",
						"address3", "postal", "city", "state", "countryid", "langid",
						"telephone", "mobile", "email", "telefax", "website", "longitude", "latitude",
						"pos", "birthday", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_address"
					SET :names
						"parentid" = ?, "addrid" = ?, "type" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
						"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?, "address2" = ?,
						"address3" = ?, "postal" = ?, "city" = ?, "state" = ?, "countryid" = ?, "langid" = ?,
						"telephone" = ?, "mobile" = ?, "email" = ?, "telefax" = ?, "website" = ?,
						"longitude" = ?, "latitude" = ?, "pos" = ?, "birthday" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_address" mordad
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_address" mordad
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordad."id" ) AS "count"
					FROM "mshop_order_address" mordad
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_address_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
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
						GROUP BY mordco.id, :cols
						ORDER BY mordco.id DESC
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
						GROUP BY mordco.id, :cols
						ORDER BY mordco.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_coupon"
					WHERE :cond AND "siteid" LIKE ?
					'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_coupon" ( :names
						"parentid", "ordprodid", "code", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_coupon"
					SET :names
						"parentid" = ?, "ordprodid" = ?, "code" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_coupon" mordco
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_coupon" mordco
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordco."id" ) AS "count"
					FROM "mshop_order_coupon" mordco
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_coupon_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'product' => array(
			'attribute' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_product_attr" mordprat
							:joins
							WHERE :cond
							GROUP BY mordprat.id, :cols
							ORDER BY mordprat.id DESC
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
							GROUP BY mordprat.id, :cols
							ORDER BY mordprat.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_product_attr"
						WHERE :cond AND "siteid" LIKE ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_product_attr" ( :names
							"attrid", "parentid", "type", "code", "value",
							"quantity", "price", "name", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_product_attr"
						SET :names
							"attrid" = ?, "parentid" = ?, "type" = ?, "code" = ?, "value" = ?,
							"quantity" = ?, "price" = ?, "name" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
						FROM "mshop_order_product_attr" mordprat
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
						FROM "mshop_order_product_attr" mordprat
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordprat."id" ) AS "count"
						FROM "mshop_order_product_attr" mordprat
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_product_attr_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
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
						GROUP BY mordpr.id, :cols
						ORDER BY mordpr.id DESC
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
						GROUP BY mordpr.id, :cols
						ORDER BY mordpr.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_product"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_product" ( :names
						"parentid", "ordprodid", "ordaddrid", "type", "parentprodid", "prodid", "prodcode",
						"vendor", "stocktype", "name", "description", "mediaurl", "timeframe",
						"quantity", "currencyid", "price", "costs", "rebate", "tax", "taxrate", "taxflag",
						"flags", "statuspayment", "statusdelivery", "pos", "mtime", "editor", "target",
						"qtyopen", "notes", "scale", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_product"
					SET :names
						"parentid" = ?, "ordprodid" = ?, "ordaddrid" = ?, "type" = ?, "parentprodid" = ?,
						"prodid" = ?, "prodcode" = ?, "vendor" = ?, "stocktype" = ?,
						"name" = ?, "description" = ?, "mediaurl" = ?, "timeframe" = ?, "quantity" = ?,
						"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?,
						"taxflag" = ?, "flags" = ?, "statuspayment" = ?, "statusdelivery" = ?, "pos" = ?,
						"mtime" = ?, "editor" = ?, "target" = ?, "qtyopen" = ?, "notes" = ?, "scale" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_product" mordpr
					:joins
					WHERE :cond
					GROUP BY :group
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_product" mordpr
					:joins
					WHERE :cond
					GROUP BY :group
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordpr."id" ) AS "count"
					FROM "mshop_order_product" mordpr
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_product_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'service' => array(
			'attribute' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :type(:val) AS "val"
							FROM "mshop_order_service_attr" mordseat
							:joins
							WHERE :cond
							GROUP BY mordseat.id, :cols
							ORDER BY mordseat.id DESC
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
							GROUP BY mordseat.id, :cols
							ORDER BY mordseat.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_service_attr"
						WHERE :cond AND "siteid" LIKE ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_service_attr" ( :names
							"attrid", "parentid", "type", "code", "value",
							"quantity", "price", "name", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_service_attr"
						SET :names
							"attrid" = ?, "parentid" = ?, "type" = ?, "code" = ?, "value" = ?,
							"quantity" = ?, "price" = ?, "name" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
						FROM "mshop_order_service_attr" mordseat
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
						FROM "mshop_order_service_attr" mordseat
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordseat."id" ) AS "count"
						FROM "mshop_order_service_attr" mordseat
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_service_attr_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
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
							GROUP BY mordsetx.id, :cols
							ORDER BY mordsetx.id DESC
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
							GROUP BY mordsetx.id, :cols
							ORDER BY mordsetx.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_service_tx"
						WHERE :cond AND "siteid" LIKE ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_service_tx" ( :names
							"parentid", "type", "currencyid", "price", "costs", "rebate", "tax", "taxflag",
							"status", "config", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_service_tx"
						SET :names
							"parentid" = ?, "type" = ?, "currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
							"tax" = ?, "taxflag" = ?, "status" = ?, "config" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
						FROM "mshop_order_service_tx" mordsetx
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
						FROM "mshop_order_service_tx" mordsetx
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordsetx."id" ) AS "count"
						FROM "mshop_order_service_tx" mordsetx
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_service_tx_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
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
						GROUP BY mordse.id, :cols
						ORDER BY mordse.id DESC
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
						GROUP BY mordse.id, :cols
						ORDER BY mordse.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_service"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_service" ( :names
						"parentid", "servid", "type", "code", "name", "mediaurl",
						"currencyid", "price", "costs", "rebate", "tax", "taxrate",
						"taxflag", "pos", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_service"
					SET :names
						"parentid" = ?, "servid" = ?, "type" = ?, "code" = ?,
						"name" = ?, "mediaurl" = ?, "currencyid" = ?, "price" = ?,
						"costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?,
						"taxflag" = ?, "pos" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_service" mordse
					:joins
					WHERE :cond
					GROUP BY :group
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_service" mordse
					:joins
					WHERE :cond
					GROUP BY :group
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordse."id" ) AS "count"
					FROM "mshop_order_service" mordse
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_service_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
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
						GROUP BY mordst.id, :cols
						ORDER BY mordst.id DESC
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
						GROUP BY mordst.id, :cols
						ORDER BY mordst.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_status"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_status" ( :names
						"parentid", "type", "value", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_status"
					SET :names
						"parentid" = ?, "type" = ?, "value" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_order_status" mordst
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_order_status" mordst
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordst."id" ) AS "count"
					FROM "mshop_order_status" mordst
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_status_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
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
					GROUP BY mord.id, :cols
					ORDER BY mord.id DESC
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
					GROUP BY mord.id, :cols
					ORDER BY mord.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_order" ( :names
					"invoiceno", "channel", "datepayment", "datedelivery",
					"statusdelivery", "statuspayment", "relatedid",
					"customerid", "sitecode", "langid", "currencyid",
					"price", "costs", "rebate", "tax", "taxflag", "customerref",
					"comment", "mtime", "editor", "siteid", "ctime",
					"cdate", "cmonth", "cweek", "cwday", "chour"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_order"
				SET :names
					"invoiceno" = ?, "channel" = ?, "datepayment" = ?, "datedelivery" = ?,
					"statusdelivery" = ?, "statuspayment" = ?, "relatedid" = ?,
					"customerid" = ?, "sitecode" = ?, "langid" = ?, "currencyid" = ?,
					"price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxflag" = ?,
					"customerref" = ?, "comment" = ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" LIKE ? AND "id" = ?
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_order"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_order" mord
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_order" mord
				:joins
				WHERE :cond
				GROUP BY :group
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT( DISTINCT mord."id" ) AS "count"
				FROM "mshop_order" mord
				:joins
				WHERE :cond
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_order_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
		'subdomains' => [
			'order/address' => 'order/address',
			'order/coupon' => 'order/coupon',
			'order/product' => 'order/product',
			'order/service' => 'order/service',
		],
	),
);
