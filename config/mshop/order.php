<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
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
						"telephone", "email", "telefax", "website", "longitude", "latitude",
						"pos", "birthday", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
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
						"telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
						"pos" = ?, "birthday" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mordad."id" AS "order.address.id", mordad."parentid" AS "order.address.parentid",
						mordad."siteid" AS "order.address.siteid", mordad."addrid" AS "order.address.addressid",
						mordad."type" AS "order.address.type", mordad."company" AS "order.address.company",
						mordad."vatid" AS "order.address.vatid", mordad."salutation" AS "order.address.salutation",
						mordad."title" AS "order.address.title", mordad."firstname" AS "order.address.firstname",
						mordad."lastname" AS "order.address.lastname", mordad."address1" AS "order.address.address1",
						mordad."address2" AS "order.address.address2", mordad."address3" AS "order.address.address3",
						mordad."postal" AS "order.address.postal", mordad."city" AS "order.address.city",
						mordad."state" AS "order.address.state", mordad."countryid" AS "order.address.countryid",
						mordad."langid" AS "order.address.languageid", mordad."telephone" AS "order.address.telephone",
						mordad."email" AS "order.address.email", mordad."telefax" AS "order.address.telefax",
						mordad."website" AS "order.address.website", mordad."longitude" AS "order.address.longitude",
						mordad."latitude" AS "order.address.latitude", mordad."pos" AS "order.address.position",
						mordad."mtime" AS "order.address.mtime", mordad."editor" AS "order.address.editor",
						mordad."ctime" AS "order.address.ctime", mordad."birthday" AS "order.address.birthday"
					FROM "mshop_order_address" mordad
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordad."id" AS "order.address.id", mordad."parentid" AS "order.address.parentid",
						mordad."siteid" AS "order.address.siteid", mordad."addrid" AS "order.address.addressid",
						mordad."type" AS "order.address.type", mordad."company" AS "order.address.company",
						mordad."vatid" AS "order.address.vatid", mordad."salutation" AS "order.address.salutation",
						mordad."title" AS "order.address.title", mordad."firstname" AS "order.address.firstname",
						mordad."lastname" AS "order.address.lastname", mordad."address1" AS "order.address.address1",
						mordad."address2" AS "order.address.address2", mordad."address3" AS "order.address.address3",
						mordad."postal" AS "order.address.postal", mordad."city" AS "order.address.city",
						mordad."state" AS "order.address.state", mordad."countryid" AS "order.address.countryid",
						mordad."langid" AS "order.address.languageid", mordad."telephone" AS "order.address.telephone",
						mordad."email" AS "order.address.email", mordad."telefax" AS "order.address.telefax",
						mordad."website" AS "order.address.website", mordad."longitude" AS "order.address.longitude",
						mordad."latitude" AS "order.address.latitude", mordad."pos" AS "order.address.position",
						mordad."mtime" AS "order.address.mtime", mordad."editor" AS "order.address.editor",
						mordad."ctime" AS "order.address.ctime", mordad."birthday" AS "order.address.birthday"
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
						mordco."id" AS "order.coupon.id", mordco."parentid" AS "order.coupon.parentid",
						mordco."siteid" AS "order.coupon.siteid", mordco."ordprodid" AS "order.coupon.ordprodid",
						mordco."code" AS "order.coupon.code", mordco."mtime" AS "order.coupon.mtime",
						mordco."editor" AS "order.coupon.editor", mordco."ctime" AS "order.coupon.ctime"
					FROM "mshop_order_coupon" mordco
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordco."id" AS "order.coupon.id", mordco."parentid" AS "order.coupon.parentid",
						mordco."siteid" AS "order.coupon.siteid", mordco."ordprodid" AS "order.coupon.ordprodid",
						mordco."code" AS "order.coupon.code", mordco."mtime" AS "order.coupon.mtime",
						mordco."editor" AS "order.coupon.editor", mordco."ctime" AS "order.coupon.ctime"
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
							mordprat."id" AS "order.product.attribute.id", mordprat."siteid" AS "order.product.attribute.siteid",
							mordprat."attrid" AS "order.product.attribute.attributeid", mordprat."parentid" AS "order.product.attribute.parentid",
							mordprat."type" AS "order.product.attribute.type", mordprat."code" AS "order.product.attribute.code",
							mordprat."value" AS "order.product.attribute.value", mordprat."quantity" AS "order.product.attribute.quantity",
							mordprat."name" AS "order.product.attribute.name", mordprat."mtime" AS "order.product.attribute.mtime",
							mordprat."editor" AS "order.product.attribute.editor", mordprat."ctime" AS "order.product.attribute.ctime",
							mordprat."price" AS "order.product.attribute.price"
						FROM "mshop_order_product_attr" mordprat
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordprat."id" AS "order.product.attribute.id", mordprat."siteid" AS "order.product.attribute.siteid",
							mordprat."attrid" AS "order.product.attribute.attributeid", mordprat."parentid" AS "order.product.attribute.parentid",
							mordprat."type" AS "order.product.attribute.type", mordprat."code" AS "order.product.attribute.code",
							mordprat."value" AS "order.product.attribute.value", mordprat."quantity" AS "order.product.attribute.quantity",
							mordprat."name" AS "order.product.attribute.name", mordprat."mtime" AS "order.product.attribute.mtime",
							mordprat."editor" AS "order.product.attribute.editor", mordprat."ctime" AS "order.product.attribute.ctime",
							mordprat."price" AS "order.product.attribute.price"
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
						mordpr."id" AS "order.product.id", mordpr."parentid" AS "order.product.parentid",
						mordpr."siteid" AS "order.product.siteid", mordpr."ordprodid" AS "order.product.orderproductid",
						mordpr."prodid" AS "order.product.productid", mordpr."prodcode" AS "order.product.prodcode",
						mordpr."description" AS "order.product.description", mordpr."stocktype" AS "order.product.stocktype",
						mordpr."type" AS "order.product.type", mordpr."name" AS "order.product.name",
						mordpr."mediaurl" AS "order.product.mediaurl", mordpr."timeframe" AS "order.product.timeframe",
						mordpr."quantity" AS "order.product.quantity", mordpr."currencyid" AS "order.product.currencyid",
						mordpr."price" AS "order.product.price", mordpr."costs" AS "order.product.costs",
						mordpr."rebate" AS "order.product.rebate", mordpr."tax" AS "order.product.taxvalue",
						mordpr."taxrate" AS "order.product.taxrates", mordpr."taxflag" AS "order.product.taxflag",
						mordpr."flags" AS "order.product.flags", mordpr."statusdelivery" AS "order.product.statusdelivery",
						mordpr."pos" AS "order.product.position", mordpr."mtime" AS "order.product.mtime",
						mordpr."editor" AS "order.product.editor", mordpr."ctime" AS "order.product.ctime",
						mordpr."target" AS "order.product.target", mordpr."ordaddrid" AS "order.product.orderaddressid",
						mordpr."vendor" AS "order.product.vendor", mordpr."scale" AS "order.product.scale",
						mordpr."qtyopen" AS "order.product.qtyopen", mordpr."notes" AS "order.product.notes",
						mordpr."statuspayment" AS "order.product.statuspayment", mordpr."parentprodid" AS "order.product.parentproductid"
					FROM "mshop_order_product" mordpr
					:joins
					WHERE :cond
					GROUP BY :columns :group
						mordpr."id", mordpr."parentid", mordpr."siteid", mordpr."ordprodid", mordpr."prodid",
						mordpr."prodcode", mordpr."description", mordpr."stocktype", mordpr."type",
						mordpr."name", mordpr."mediaurl", mordpr."timeframe", mordpr."quantity",
						mordpr."currencyid", mordpr."price", mordpr."costs", mordpr."rebate", mordpr."tax",
						mordpr."taxrate", mordpr."taxflag", mordpr."flags", mordpr."statusdelivery", mordpr."pos",
						mordpr."mtime", mordpr."editor", mordpr."ctime", mordpr."target", mordpr."ordaddrid",
						mordpr."vendor", mordpr."qtyopen", mordpr."notes", mordpr."scale",
						mordpr."statuspayment", mordpr."parentprodid"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordpr."id" AS "order.product.id", mordpr."parentid" AS "order.product.parentid",
						mordpr."siteid" AS "order.product.siteid", mordpr."ordprodid" AS "order.product.orderproductid",
						mordpr."prodid" AS "order.product.productid", mordpr."prodcode" AS "order.product.prodcode",
						mordpr."description" AS "order.product.description", mordpr."stocktype" AS "order.product.stocktype",
						mordpr."type" AS "order.product.type", mordpr."name" AS "order.product.name",
						mordpr."mediaurl" AS "order.product.mediaurl", mordpr."timeframe" AS "order.product.timeframe",
						mordpr."quantity" AS "order.product.quantity", mordpr."currencyid" AS "order.product.currencyid",
						mordpr."price" AS "order.product.price", mordpr."costs" AS "order.product.costs",
						mordpr."rebate" AS "order.product.rebate", mordpr."tax" AS "order.product.taxvalue",
						mordpr."taxrate" AS "order.product.taxrates", mordpr."taxflag" AS "order.product.taxflag",
						mordpr."flags" AS "order.product.flags", mordpr."statusdelivery" AS "order.product.statusdelivery",
						mordpr."pos" AS "order.product.position", mordpr."mtime" AS "order.product.mtime",
						mordpr."editor" AS "order.product.editor", mordpr."ctime" AS "order.product.ctime",
						mordpr."target" AS "order.product.target", mordpr."ordaddrid" AS "order.product.orderaddressid",
						mordpr."vendor" AS "order.product.vendor", mordpr."scale" AS "order.product.scale",
						mordpr."qtyopen" AS "order.product.qtyopen", mordpr."notes" AS "order.product.notes",
						mordpr."statuspayment" AS "order.product.statuspayment", mordpr."parentprodid" AS "order.product.parentproductid"
					FROM "mshop_order_product" mordpr
					:joins
					WHERE :cond
					GROUP BY :group mordpr."id"
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
							mordseat."id" AS "order.service.attribute.id", mordseat."siteid" AS "order.service.attribute.siteid",
							mordseat."attrid" AS "order.service.attribute.attributeid", mordseat."parentid" AS "order.service.attribute.parentid",
							mordseat."type" AS "order.service.attribute.type", mordseat."code" AS "order.service.attribute.code",
							mordseat."value" AS "order.service.attribute.value", mordseat."quantity" AS "order.service.attribute.quantity",
							mordseat."name" AS "order.service.attribute.name", mordseat."mtime" AS "order.service.attribute.mtime",
							mordseat."ctime" AS "order.service.attribute.ctime", mordseat."editor" AS "order.service.attribute.editor",
							mordseat."price" AS "order.service.attribute.price"
						FROM "mshop_order_service_attr" mordseat
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordseat."id" AS "order.service.attribute.id", mordseat."siteid" AS "order.service.attribute.siteid",
							mordseat."attrid" AS "order.service.attribute.attributeid", mordseat."parentid" AS "order.service.attribute.parentid",
							mordseat."type" AS "order.service.attribute.type", mordseat."code" AS "order.service.attribute.code",
							mordseat."value" AS "order.service.attribute.value", mordseat."quantity" AS "order.service.attribute.quantity",
							mordseat."name" AS "order.service.attribute.name", mordseat."mtime" AS "order.service.attribute.mtime",
							mordseat."ctime" AS "order.service.attribute.ctime", mordseat."editor" AS "order.service.attribute.editor",
							mordseat."price" AS "order.service.attribute.price"
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
							mordsetx."id" AS "order.service.transaction.id", mordsetx."siteid" AS "order.service.transaction.siteid",
							mordsetx."parentid" AS "order.service.transaction.parentid", mordsetx."type" AS "order.service.transaction.type",
							mordsetx."currencyid" AS "order.service.transaction.currencyid", mordsetx."price" AS "order.service.transaction.price",
							mordsetx."costs" AS "order.service.transaction.costs", mordsetx."rebate" AS "order.service.transaction.rebate",
							mordsetx."tax" AS "order.service.transaction.taxvalue", mordsetx."taxflag" AS "order.service.transaction.taxflag",
							mordsetx."config" AS "order.service.transaction.config", mordsetx."status" AS "order.service.transaction.status",
							mordsetx."mtime" AS "order.service.transaction.mtime", mordsetx."ctime" AS "order.service.transaction.ctime",
							mordsetx."editor" AS "order.service.transaction.editor"
						FROM "mshop_order_service_tx" mordsetx
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordsetx."id" AS "order.service.transaction.id", mordsetx."siteid" AS "order.service.transaction.siteid",
							mordsetx."parentid" AS "order.service.transaction.parentid", mordsetx."type" AS "order.service.transaction.type",
							mordsetx."currencyid" AS "order.service.transaction.currencyid", mordsetx."price" AS "order.service.transaction.price",
							mordsetx."costs" AS "order.service.transaction.costs", mordsetx."rebate" AS "order.service.transaction.rebate",
							mordsetx."tax" AS "order.service.transaction.taxvalue", mordsetx."taxflag" AS "order.service.transaction.taxflag",
							mordsetx."config" AS "order.service.transaction.config", mordsetx."status" AS "order.service.transaction.status",
							mordsetx."mtime" AS "order.service.transaction.mtime", mordsetx."ctime" AS "order.service.transaction.ctime",
							mordsetx."editor" AS "order.service.transaction.editor"
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
						mordse."id" AS "order.service.id", mordse."parentid" AS "order.service.parentid",
						mordse."siteid" AS "order.service.siteid", mordse."servid" AS "order.service.serviceid",
						mordse."type" AS "order.service.type", mordse."code" AS "order.service.code",
						mordse."name" AS "order.service.name", mordse."mediaurl" AS "order.service.mediaurl",
						mordse."currencyid" AS "order.service.currencyid", mordse."price" AS "order.service.price",
						mordse."costs" AS "order.service.costs", mordse."rebate" AS "order.service.rebate",
						mordse."tax" AS "order.service.taxvalue", mordse."taxrate" AS "order.service.taxrates",
						mordse."taxflag" AS "order.service.taxflag", mordse."pos" AS "order.service.position",
						mordse."mtime" AS "order.service.mtime", mordse."editor" AS "order.service.editor",
						mordse."ctime" AS "order.service.ctime"
					FROM "mshop_order_service" mordse
					:joins
					WHERE :cond
					GROUP BY :columns :group
						mordse."id", mordse."parentid", mordse."siteid", mordse."servid", mordse."type",
						mordse."code", mordse."name", mordse."mediaurl", mordse."currencyid", mordse."price",
						mordse."costs", mordse."rebate", mordse."tax", mordse."taxrate", mordse."taxflag",
						mordse."pos", mordse."mtime", mordse."editor", mordse."ctime"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordse."id" AS "order.service.id", mordse."parentid" AS "order.service.parentid",
						mordse."siteid" AS "order.service.siteid", mordse."servid" AS "order.service.serviceid",
						mordse."type" AS "order.service.type", mordse."code" AS "order.service.code",
						mordse."name" AS "order.service.name", mordse."mediaurl" AS "order.service.mediaurl",
						mordse."currencyid" AS "order.service.currencyid", mordse."price" AS "order.service.price",
						mordse."costs" AS "order.service.costs", mordse."rebate" AS "order.service.rebate",
						mordse."tax" AS "order.service.taxvalue", mordse."taxrate" AS "order.service.taxrates",
						mordse."taxflag" AS "order.service.taxflag", mordse."pos" AS "order.service.position",
						mordse."mtime" AS "order.service.mtime", mordse."editor" AS "order.service.editor",
						mordse."ctime" AS "order.service.ctime"
					FROM "mshop_order_service" mordse
					:joins
					WHERE :cond
					GROUP BY :group mordse."id"
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
					INSERT INTO "mshop_order_basket" (
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) VALUES (
						?, ?, ?, ?, ?, ?, ?, ?
					) ON DUPLICATE KEY UPDATE
						"customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
				',
				'pgsql' => '
					INSERT INTO "mshop_order_basket" (
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) VALUES (
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
						INSERT (
							"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
						) VALUES (
							src."customerid", src."content", src."name", src."mtime", src."editor", src."siteid", src."ctime", src."id"
						);
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mordba."id" AS "order.basket.id", mordba."siteid" AS "order.basket.siteid",
						mordba."customerid" AS "order.basket.customerid", mordba."name" AS "order.basket.name",
						mordba."content" AS "order.basket.content", mordba."mtime" AS "order.basket.mtime",
						mordba."ctime" AS "order.basket.ctime", mordba."editor" AS "order.basket.editor"
					FROM "mshop_order_basket" mordba
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordba."id" AS "order.basket.id", mordba."siteid" AS "order.basket.siteid",
						mordba."customerid" AS "order.basket.customerid", mordba."name" AS "order.basket.name",
						mordba."content" AS "order.basket.content", mordba."mtime" AS "order.basket.mtime",
						mordba."ctime" AS "order.basket.ctime", mordba."editor" AS "order.basket.editor"
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
						mordst."id" AS "order.status.id", mordst."siteid" AS "order.status.siteid",
						mordst."parentid" AS "order.status.parentid", mordst."type" AS "order.status.type",
						mordst."value" AS "order.status.value", mordst."mtime" AS "order.status.mtime",
						mordst."ctime" AS "order.status.ctime", mordst."editor" AS "order.status.editor"
					FROM "mshop_order_status" mordst
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordst."id" AS "order.status.id", mordst."siteid" AS "order.status.siteid",
						mordst."parentid" AS "order.status.parentid", mordst."type" AS "order.status.type",
						mordst."value" AS "order.status.value", mordst."mtime" AS "order.status.mtime",
						mordst."ctime" AS "order.status.ctime", mordst."editor" AS "order.status.editor"
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
					mord."id" AS "order.id", mord."channel" AS "order.channel",
					mord."siteid" AS "order.siteid", mord."invoiceno" AS "order.invoiceno",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid",
					mord."sitecode" AS "order.sitecode", mord."customerid" AS "order.customerid",
					mord."langid" AS "order.languageid", mord."currencyid" AS "order.currencyid",
					mord."price" AS "order.price", mord."costs" AS "order.costs",
					mord."rebate" AS "order.rebate", mord."tax" AS "order.taxvalue",
					mord."taxflag" AS "order.taxflag", mord."customerref" AS "order.customerref",
					mord."comment" AS "order.comment", mord."ctime" AS "order.ctime",
					mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
				FROM "mshop_order" mord
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mord."id", mord."invoiceno", mord."siteid", mord."channel", mord."datepayment",
					mord."datedelivery", mord."statuspayment", mord."statusdelivery", mord."relatedid",
					mord."sitecode", mord."customerid", mord."langid", mord."currencyid", mord."price",
					mord."costs", mord."rebate", mord."tax", mord."taxflag", mord."customerref", mord."comment",
					mord."ctime", mord."mtime", mord."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mord."id" AS "order.id", mord."channel" AS "order.channel",
					mord."siteid" AS "order.siteid", mord."invoiceno" AS "order.invoiceno",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid",
					mord."sitecode" AS "order.sitecode", mord."customerid" AS "order.customerid",
					mord."langid" AS "order.languageid", mord."currencyid" AS "order.currencyid",
					mord."price" AS "order.price", mord."costs" AS "order.costs",
					mord."rebate" AS "order.rebate", mord."tax" AS "order.taxvalue",
					mord."taxflag" AS "order.taxflag", mord."customerref" AS "order.customerref",
					mord."comment" AS "order.comment", mord."ctime" AS "order.ctime",
					mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
				FROM "mshop_order" mord
				:joins
				WHERE :cond
				GROUP BY :group mord."id"
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
