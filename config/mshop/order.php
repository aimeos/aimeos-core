<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


return array(
	'manager' => array(
		'base' => array(
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
							"baseid", "addrid", "type", "company", "vatid", "salutation",
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
							"baseid" = ?, "addrid" = ?, "type" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
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
							mordad."id" AS "order.base.address.id", mordad."baseid" AS "order.base.address.baseid",
							mordad."siteid" AS "order.base.address.siteid", mordad."addrid" AS "order.base.address.addressid",
							mordad."type" AS "order.base.address.type", mordad."company" AS "order.base.address.company",
							mordad."vatid" AS "order.base.address.vatid", mordad."salutation" AS "order.base.address.salutation",
							mordad."title" AS "order.base.address.title", mordad."firstname" AS "order.base.address.firstname",
							mordad."lastname" AS "order.base.address.lastname", mordad."address1" AS "order.base.address.address1",
							mordad."address2" AS "order.base.address.address2", mordad."address3" AS "order.base.address.address3",
							mordad."postal" AS "order.base.address.postal", mordad."city" AS "order.base.address.city",
							mordad."state" AS "order.base.address.state", mordad."countryid" AS "order.base.address.countryid",
							mordad."langid" AS "order.base.address.languageid", mordad."telephone" AS "order.base.address.telephone",
							mordad."email" AS "order.base.address.email", mordad."telefax" AS "order.base.address.telefax",
							mordad."website" AS "order.base.address.website", mordad."longitude" AS "order.base.address.longitude",
							mordad."latitude" AS "order.base.address.latitude", mordad."pos" AS "order.base.address.position",
							mordad."mtime" AS "order.base.address.mtime", mordad."editor" AS "order.base.address.editor",
							mordad."ctime" AS "order.base.address.ctime", mordad."birthday" AS "order.base.address.birthday"
						FROM "mshop_order_address" mordad
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordad."id" AS "order.base.address.id", mordad."baseid" AS "order.base.address.baseid",
							mordad."siteid" AS "order.base.address.siteid", mordad."addrid" AS "order.base.address.addressid",
							mordad."type" AS "order.base.address.type", mordad."company" AS "order.base.address.company",
							mordad."vatid" AS "order.base.address.vatid", mordad."salutation" AS "order.base.address.salutation",
							mordad."title" AS "order.base.address.title", mordad."firstname" AS "order.base.address.firstname",
							mordad."lastname" AS "order.base.address.lastname", mordad."address1" AS "order.base.address.address1",
							mordad."address2" AS "order.base.address.address2", mordad."address3" AS "order.base.address.address3",
							mordad."postal" AS "order.base.address.postal", mordad."city" AS "order.base.address.city",
							mordad."state" AS "order.base.address.state", mordad."countryid" AS "order.base.address.countryid",
							mordad."langid" AS "order.base.address.languageid", mordad."telephone" AS "order.base.address.telephone",
							mordad."email" AS "order.base.address.email", mordad."telefax" AS "order.base.address.telefax",
							mordad."website" AS "order.base.address.website", mordad."longitude" AS "order.base.address.longitude",
							mordad."latitude" AS "order.base.address.latitude", mordad."pos" AS "order.base.address.position",
							mordad."mtime" AS "order.base.address.mtime", mordad."editor" AS "order.base.address.editor",
							mordad."ctime" AS "order.base.address.ctime", mordad."birthday" AS "order.base.address.birthday"
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
							"baseid", "ordprodid", "code", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_coupon"
						SET :names
							"baseid" = ?, "ordprodid" = ?, "code" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordco."id" AS "order.base.coupon.id", mordco."baseid" AS "order.base.coupon.baseid",
							mordco."siteid" AS "order.base.coupon.siteid", mordco."ordprodid" AS "order.base.coupon.ordprodid",
							mordco."code" AS "order.base.coupon.code", mordco."mtime" AS "order.base.coupon.mtime",
							mordco."editor" AS "order.base.coupon.editor", mordco."ctime" AS "order.base.coupon.ctime"
						FROM "mshop_order_coupon" mordco
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordco."id" AS "order.base.coupon.id", mordco."baseid" AS "order.base.coupon.baseid",
							mordco."siteid" AS "order.base.coupon.siteid", mordco."ordprodid" AS "order.base.coupon.ordprodid",
							mordco."code" AS "order.base.coupon.code", mordco."mtime" AS "order.base.coupon.mtime",
							mordco."editor" AS "order.base.coupon.editor", mordco."ctime" AS "order.base.coupon.ctime"
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
								mordprat."id" AS "order.base.product.attribute.id", mordprat."siteid" AS "order.base.product.attribute.siteid",
								mordprat."attrid" AS "order.base.product.attribute.attributeid", mordprat."parentid" AS "order.base.product.attribute.parentid",
								mordprat."type" AS "order.base.product.attribute.type", mordprat."code" AS "order.base.product.attribute.code",
								mordprat."value" AS "order.base.product.attribute.value", mordprat."quantity" AS "order.base.product.attribute.quantity",
								mordprat."name" AS "order.base.product.attribute.name", mordprat."mtime" AS "order.base.product.attribute.mtime",
								mordprat."editor" AS "order.base.product.attribute.editor", mordprat."ctime" AS "order.base.product.attribute.ctime",
								mordprat."price" AS "order.base.product.attribute.price"
							FROM "mshop_order_product_attr" mordprat
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mordprat."id" AS "order.base.product.attribute.id", mordprat."siteid" AS "order.base.product.attribute.siteid",
								mordprat."attrid" AS "order.base.product.attribute.attributeid", mordprat."parentid" AS "order.base.product.attribute.parentid",
								mordprat."type" AS "order.base.product.attribute.type", mordprat."code" AS "order.base.product.attribute.code",
								mordprat."value" AS "order.base.product.attribute.value", mordprat."quantity" AS "order.base.product.attribute.quantity",
								mordprat."name" AS "order.base.product.attribute.name", mordprat."mtime" AS "order.base.product.attribute.mtime",
								mordprat."editor" AS "order.base.product.attribute.editor", mordprat."ctime" AS "order.base.product.attribute.ctime",
								mordprat."price" AS "order.base.product.attribute.price"
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
							"baseid", "ordprodid", "ordaddrid", "type", "parentprodid", "prodid", "prodcode",
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
							"baseid" = ?, "ordprodid" = ?, "ordaddrid" = ?, "type" = ?, "parentprodid" = ?,
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
							mordpr."id" AS "order.base.product.id", mordpr."baseid" AS "order.base.product.baseid",
							mordpr."siteid" AS "order.base.product.siteid", mordpr."ordprodid" AS "order.base.product.orderproductid",
							mordpr."prodid" AS "order.base.product.productid", mordpr."prodcode" AS "order.base.product.prodcode",
							mordpr."description" AS "order.base.product.description", mordpr."stocktype" AS "order.base.product.stocktype",
							mordpr."type" AS "order.base.product.type", mordpr."name" AS "order.base.product.name",
							mordpr."mediaurl" AS "order.base.product.mediaurl", mordpr."timeframe" AS "order.base.product.timeframe",
							mordpr."quantity" AS "order.base.product.quantity", mordpr."currencyid" AS "order.base.product.currencyid",
							mordpr."price" AS "order.base.product.price", mordpr."costs" AS "order.base.product.costs",
							mordpr."rebate" AS "order.base.product.rebate", mordpr."tax" AS "order.base.product.taxvalue",
							mordpr."taxrate" AS "order.base.product.taxrates", mordpr."taxflag" AS "order.base.product.taxflag",
							mordpr."flags" AS "order.base.product.flags", mordpr."statusdelivery" AS "order.base.product.statusdelivery",
							mordpr."pos" AS "order.base.product.position", mordpr."mtime" AS "order.base.product.mtime",
							mordpr."editor" AS "order.base.product.editor", mordpr."ctime" AS "order.base.product.ctime",
							mordpr."target" AS "order.base.product.target", mordpr."ordaddrid" AS "order.base.product.orderaddressid",
							mordpr."vendor" AS "order.base.product.vendor", mordpr."scale" AS "order.base.product.scale",
							mordpr."qtyopen" AS "order.base.product.qtyopen", mordpr."notes" AS "order.base.product.notes",
							mordpr."statuspayment" AS "order.base.product.statuspayment", mordpr."parentprodid" AS "order.base.product.parentproductid"
						FROM "mshop_order_product" mordpr
						:joins
						WHERE :cond
						GROUP BY :columns :group
							mordpr."id", mordpr."baseid", mordpr."siteid", mordpr."ordprodid", mordpr."prodid",
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
							mordpr."id" AS "order.base.product.id", mordpr."baseid" AS "order.base.product.baseid",
							mordpr."siteid" AS "order.base.product.siteid", mordpr."ordprodid" AS "order.base.product.orderproductid",
							mordpr."prodid" AS "order.base.product.productid", mordpr."prodcode" AS "order.base.product.prodcode",
							mordpr."description" AS "order.base.product.description", mordpr."stocktype" AS "order.base.product.stocktype",
							mordpr."type" AS "order.base.product.type", mordpr."name" AS "order.base.product.name",
							mordpr."mediaurl" AS "order.base.product.mediaurl", mordpr."timeframe" AS "order.base.product.timeframe",
							mordpr."quantity" AS "order.base.product.quantity", mordpr."currencyid" AS "order.base.product.currencyid",
							mordpr."price" AS "order.base.product.price", mordpr."costs" AS "order.base.product.costs",
							mordpr."rebate" AS "order.base.product.rebate", mordpr."tax" AS "order.base.product.taxvalue",
							mordpr."taxrate" AS "order.base.product.taxrates", mordpr."taxflag" AS "order.base.product.taxflag",
							mordpr."flags" AS "order.base.product.flags", mordpr."statusdelivery" AS "order.base.product.statusdelivery",
							mordpr."pos" AS "order.base.product.position", mordpr."mtime" AS "order.base.product.mtime",
							mordpr."editor" AS "order.base.product.editor", mordpr."ctime" AS "order.base.product.ctime",
							mordpr."target" AS "order.base.product.target", mordpr."ordaddrid" AS "order.base.product.orderaddressid",
							mordpr."vendor" AS "order.base.product.vendor", mordpr."scale" AS "order.base.product.scale",
							mordpr."qtyopen" AS "order.base.product.qtyopen", mordpr."notes" AS "order.base.product.notes",
							mordpr."statuspayment" AS "order.base.product.statuspayment", mordpr."parentprodid" AS "order.base.product.parentproductid"
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
								mordseat."id" AS "order.base.service.attribute.id", mordseat."siteid" AS "order.base.service.attribute.siteid",
								mordseat."attrid" AS "order.base.service.attribute.attributeid", mordseat."parentid" AS "order.base.service.attribute.parentid",
								mordseat."type" AS "order.base.service.attribute.type", mordseat."code" AS "order.base.service.attribute.code",
								mordseat."value" AS "order.base.service.attribute.value", mordseat."quantity" AS "order.base.service.attribute.quantity",
								mordseat."name" AS "order.base.service.attribute.name", mordseat."mtime" AS "order.base.service.attribute.mtime",
								mordseat."ctime" AS "order.base.service.attribute.ctime", mordseat."editor" AS "order.base.service.attribute.editor",
								mordseat."price" AS "order.base.service.attribute.price"
							FROM "mshop_order_service_attr" mordseat
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mordseat."id" AS "order.base.service.attribute.id", mordseat."siteid" AS "order.base.service.attribute.siteid",
								mordseat."attrid" AS "order.base.service.attribute.attributeid", mordseat."parentid" AS "order.base.service.attribute.parentid",
								mordseat."type" AS "order.base.service.attribute.type", mordseat."code" AS "order.base.service.attribute.code",
								mordseat."value" AS "order.base.service.attribute.value", mordseat."quantity" AS "order.base.service.attribute.quantity",
								mordseat."name" AS "order.base.service.attribute.name", mordseat."mtime" AS "order.base.service.attribute.mtime",
								mordseat."ctime" AS "order.base.service.attribute.ctime", mordseat."editor" AS "order.base.service.attribute.editor",
								mordseat."price" AS "order.base.service.attribute.price"
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
								mordsetx."id" AS "order.base.service.transaction.id", mordsetx."siteid" AS "order.base.service.transaction.siteid",
								mordsetx."parentid" AS "order.base.service.transaction.parentid", mordsetx."type" AS "order.base.service.transaction.type",
								mordsetx."currencyid" AS "order.base.service.transaction.currencyid", mordsetx."price" AS "order.base.service.transaction.price",
								mordsetx."costs" AS "order.base.service.transaction.costs", mordsetx."rebate" AS "order.base.service.transaction.rebate",
								mordsetx."tax" AS "order.base.service.transaction.taxvalue", mordsetx."taxflag" AS "order.base.service.transaction.taxflag",
								mordsetx."config" AS "order.base.service.transaction.config", mordsetx."status" AS "order.base.service.transaction.status",
								mordsetx."mtime" AS "order.base.service.transaction.mtime", mordsetx."ctime" AS "order.base.service.transaction.ctime",
								mordsetx."editor" AS "order.base.service.transaction.editor"
							FROM "mshop_order_service_tx" mordsetx
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mordsetx."id" AS "order.base.service.transaction.id", mordsetx."siteid" AS "order.base.service.transaction.siteid",
								mordsetx."parentid" AS "order.base.service.transaction.parentid", mordsetx."type" AS "order.base.service.transaction.type",
								mordsetx."currencyid" AS "order.base.service.transaction.currencyid", mordsetx."price" AS "order.base.service.transaction.price",
								mordsetx."costs" AS "order.base.service.transaction.costs", mordsetx."rebate" AS "order.base.service.transaction.rebate",
								mordsetx."tax" AS "order.base.service.transaction.taxvalue", mordsetx."taxflag" AS "order.base.service.transaction.taxflag",
								mordsetx."config" AS "order.base.service.transaction.config", mordsetx."status" AS "order.base.service.transaction.status",
								mordsetx."mtime" AS "order.base.service.transaction.mtime", mordsetx."ctime" AS "order.base.service.transaction.ctime",
								mordsetx."editor" AS "order.base.service.transaction.editor"
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
							"baseid", "servid", "type", "code", "name", "mediaurl",
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
							"baseid" = ?, "servid" = ?, "type" = ?, "code" = ?,
							"name" = ?, "mediaurl" = ?, "currencyid" = ?, "price" = ?,
							"costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?,
							"taxflag" = ?, "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordse."id" AS "order.base.service.id", mordse."baseid" AS "order.base.service.baseid",
							mordse."siteid" AS "order.base.service.siteid", mordse."servid" AS "order.base.service.serviceid",
							mordse."type" AS "order.base.service.type", mordse."code" AS "order.base.service.code",
							mordse."name" AS "order.base.service.name", mordse."mediaurl" AS "order.base.service.mediaurl",
							mordse."currencyid" AS "order.base.service.currencyid", mordse."price" AS "order.base.service.price",
							mordse."costs" AS "order.base.service.costs", mordse."rebate" AS "order.base.service.rebate",
							mordse."tax" AS "order.base.service.taxvalue", mordse."taxrate" AS "order.base.service.taxrates",
							mordse."taxflag" AS "order.base.service.taxflag", mordse."pos" AS "order.base.service.position",
							mordse."mtime" AS "order.base.service.mtime", mordse."editor" AS "order.base.service.editor",
							mordse."ctime" AS "order.base.service.ctime"
						FROM "mshop_order_service" mordse
						:joins
						WHERE :cond
						GROUP BY :columns :group
							mordse."id", mordse."baseid", mordse."siteid", mordse."servid", mordse."type",
							mordse."code", mordse."name", mordse."mediaurl", mordse."currencyid", mordse."price",
							mordse."costs", mordse."rebate", mordse."tax", mordse."taxrate", mordse."taxflag",
							mordse."pos", mordse."mtime", mordse."editor", mordse."ctime"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordse."id" AS "order.base.service.id", mordse."baseid" AS "order.base.service.baseid",
							mordse."siteid" AS "order.base.service.siteid", mordse."servid" AS "order.base.service.serviceid",
							mordse."type" AS "order.base.service.type", mordse."code" AS "order.base.service.code",
							mordse."name" AS "order.base.service.name", mordse."mediaurl" AS "order.base.service.mediaurl",
							mordse."currencyid" AS "order.base.service.currencyid", mordse."price" AS "order.base.service.price",
							mordse."costs" AS "order.base.service.costs", mordse."rebate" AS "order.base.service.rebate",
							mordse."tax" AS "order.base.service.taxvalue", mordse."taxrate" AS "order.base.service.taxrates",
							mordse."taxflag" AS "order.base.service.taxflag", mordse."pos" AS "order.base.service.position",
							mordse."mtime" AS "order.base.service.mtime", mordse."editor" AS "order.base.service.editor",
							mordse."ctime" AS "order.base.service.ctime"
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
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_base" mordba
						:joins
						WHERE :cond
						GROUP BY mordba.id, :cols
						ORDER BY mordba.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :type(:val) AS "val"
						FROM "mshop_order_base" mordba
						:joins
						WHERE :cond
						GROUP BY mordba.id, :cols
						ORDER BY mordba.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_base"
					WHERE :cond AND "siteid" LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_order_base" ( :names
						"customerid", "sitecode", "langid", "currencyid",
						"price", "costs", "rebate", "tax", "taxflag", "customerref",
						"comment", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_order_base"
					SET :names
						"customerid" = ?, "sitecode" = ?, "langid" = ?, "currencyid" = ?,
						"price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxflag" = ?,
						"customerref" = ?, "comment" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mordba."id" AS "order.base.id", mordba."siteid" AS "order.base.siteid",
						mordba."sitecode" AS "order.base.sitecode", mordba."customerid" AS "order.base.customerid",
						mordba."langid" AS "order.base.languageid", mordba."currencyid" AS "order.base.currencyid",
						mordba."price" AS "order.base.price", mordba."costs" AS "order.base.costs",
						mordba."rebate" AS "order.base.rebate", mordba."tax" AS "order.base.taxvalue",
						mordba."taxflag" AS "order.base.taxflag", mordba."customerref" AS "order.base.customerref",
						mordba."comment" AS "order.base.comment", mordba."mtime" AS "order.base.mtime",
						mordba."ctime" AS "order.base.ctime", mordba."editor" AS "order.base.editor"
					FROM "mshop_order_base" mordba
					:joins
					WHERE :cond
					GROUP BY :columns :group
						mordba."id", mordba."siteid", mordba."sitecode", mordba."customerid", mordba."langid",
						mordba."currencyid", mordba."price", mordba."costs", mordba."rebate", mordba."tax",
						mordba."taxflag", mordba."customerref", mordba."comment", mordba."mtime", mordba."ctime",
						mordba."editor"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mordba."id" AS "order.base.id", mordba."siteid" AS "order.base.siteid",
						mordba."sitecode" AS "order.base.sitecode", mordba."customerid" AS "order.base.customerid",
						mordba."langid" AS "order.base.languageid", mordba."currencyid" AS "order.base.currencyid",
						mordba."price" AS "order.base.price", mordba."costs" AS "order.base.costs",
						mordba."rebate" AS "order.base.rebate", mordba."tax" AS "order.base.taxvalue",
						mordba."taxflag" AS "order.base.taxflag", mordba."customerref" AS "order.base.customerref",
						mordba."comment" AS "order.base.comment", mordba."mtime" AS "order.base.mtime",
						mordba."ctime" AS "order.base.ctime", mordba."editor" AS "order.base.editor"
					FROM "mshop_order_base" mordba
					:joins
					WHERE :cond
					GROUP BY :group mordba."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordba."id" ) AS "count"
					FROM "mshop_order_base" mordba
					:joins
					WHERE :cond
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_order_base_seq.CURRVAL FROM DUAL',
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
					"baseid", "invoiceno", "channel", "datepayment", "datedelivery",
					"statusdelivery", "statuspayment", "relatedid", "mtime",
					"editor", "siteid", "ctime", "cdate", "cmonth", "cweek", "cwday", "chour"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_order"
				SET :names
					"baseid" = ?, "invoiceno" = ?, "channel" = ?, "datepayment" = ?, "datedelivery" = ?,
					"statusdelivery" = ?, "statuspayment" = ?, "relatedid" = ?, "mtime" = ?, "editor" = ?
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
					mord."id" AS "order.id", mord."baseid" AS "order.baseid", mord."channel" AS "order.channel",
					mord."siteid" AS "order.siteid", mord."invoiceno" AS "order.invoiceno",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid", mord."ctime" AS "order.ctime",
					mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
				FROM "mshop_order" mord
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mord."id", mord."baseid", mord."invoiceno", mord."siteid", mord."channel", mord."datepayment",
					mord."datedelivery", mord."statuspayment", mord."statusdelivery", mord."relatedid", mord."ctime",
					mord."mtime", mord."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mord."id" AS "order.id", mord."baseid" AS "order.baseid", mord."channel" AS "order.channel",
					mord."siteid" AS "order.siteid", mord."invoiceno" AS "order.invoiceno",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid", mord."ctime" AS "order.ctime",
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
	),
);
