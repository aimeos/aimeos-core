<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'base' => array(
			'address' => array(
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_address" AS mordbaad
							:joins
							WHERE :cond
							GROUP BY mordbaad.id, :cols, :val
							ORDER BY mordbaad.id DESC
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_address" AS mordbaad
							:joins
							WHERE :cond
							GROUP BY mordbaad.id, :cols, :val
							ORDER BY mordbaad.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_base_address"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_base_address" ( :names
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
						UPDATE "mshop_order_base_address"
						SET :names
							"baseid" = ?, "addrid" = ?, "type" = ?, "company" = ?, "vatid" = ?, "salutation" = ?,
							"title" = ?, "firstname" = ?, "lastname" = ?, "address1" = ?, "address2" = ?,
							"address3" = ?, "postal" = ?, "city" = ?, "state" = ?, "countryid" = ?, "langid" = ?,
							"telephone" = ?, "email" = ?, "telefax" = ?, "website" = ?, "longitude" = ?, "latitude" = ?,
							"pos" = ?, "birthday" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordbaad."id" AS "order.base.address.id", mordbaad."baseid" AS "order.base.address.baseid",
							mordbaad."siteid" AS "order.base.address.siteid", mordbaad."addrid" AS "order.base.address.addressid",
							mordbaad."type" AS "order.base.address.type", mordbaad."company" AS "order.base.address.company",
							mordbaad."vatid" AS "order.base.address.vatid", mordbaad."salutation" AS "order.base.address.salutation",
							mordbaad."title" AS "order.base.address.title", mordbaad."firstname" AS "order.base.address.firstname",
							mordbaad."lastname" AS "order.base.address.lastname", mordbaad."address1" AS "order.base.address.address1",
							mordbaad."address2" AS "order.base.address.address2", mordbaad."address3" AS "order.base.address.address3",
							mordbaad."postal" AS "order.base.address.postal", mordbaad."city" AS "order.base.address.city",
							mordbaad."state" AS "order.base.address.state", mordbaad."countryid" AS "order.base.address.countryid",
							mordbaad."langid" AS "order.base.address.languageid", mordbaad."telephone" AS "order.base.address.telephone",
							mordbaad."email" AS "order.base.address.email", mordbaad."telefax" AS "order.base.address.telefax",
							mordbaad."website" AS "order.base.address.website", mordbaad."longitude" AS "order.base.address.longitude",
							mordbaad."latitude" AS "order.base.address.latitude", mordbaad."pos" AS "order.base.address.position",
							mordbaad."mtime" AS "order.base.address.mtime", mordbaad."editor" AS "order.base.address.editor",
							mordbaad."ctime" AS "order.base.address.ctime", mordbaad."birthday" AS "order.base.address.birthday"
						FROM "mshop_order_base_address" AS mordbaad
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordbaad."id" AS "order.base.address.id", mordbaad."baseid" AS "order.base.address.baseid",
							mordbaad."siteid" AS "order.base.address.siteid", mordbaad."addrid" AS "order.base.address.addressid",
							mordbaad."type" AS "order.base.address.type", mordbaad."company" AS "order.base.address.company",
							mordbaad."vatid" AS "order.base.address.vatid", mordbaad."salutation" AS "order.base.address.salutation",
							mordbaad."title" AS "order.base.address.title", mordbaad."firstname" AS "order.base.address.firstname",
							mordbaad."lastname" AS "order.base.address.lastname", mordbaad."address1" AS "order.base.address.address1",
							mordbaad."address2" AS "order.base.address.address2", mordbaad."address3" AS "order.base.address.address3",
							mordbaad."postal" AS "order.base.address.postal", mordbaad."city" AS "order.base.address.city",
							mordbaad."state" AS "order.base.address.state", mordbaad."countryid" AS "order.base.address.countryid",
							mordbaad."langid" AS "order.base.address.languageid", mordbaad."telephone" AS "order.base.address.telephone",
							mordbaad."email" AS "order.base.address.email", mordbaad."telefax" AS "order.base.address.telefax",
							mordbaad."website" AS "order.base.address.website", mordbaad."longitude" AS "order.base.address.longitude",
							mordbaad."latitude" AS "order.base.address.latitude", mordbaad."pos" AS "order.base.address.position",
							mordbaad."mtime" AS "order.base.address.mtime", mordbaad."editor" AS "order.base.address.editor",
							mordbaad."ctime" AS "order.base.address.ctime", mordbaad."birthday" AS "order.base.address.birthday"
						FROM "mshop_order_base_address" AS mordbaad
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordbaad."id" ) AS "count"
						FROM "mshop_order_base_address" AS mordbaad
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_base_address_seq.CURRVAL FROM DUAL',
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
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_coupon" AS mordbaco
							:joins
							WHERE :cond
							GROUP BY mordbaco.id, :cols, :val
							ORDER BY mordbaco.id DESC
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_coupon" AS mordbaco
							:joins
							WHERE :cond
							GROUP BY mordbaco.id, :cols, :val
							ORDER BY mordbaco.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_base_coupon"
						WHERE :cond AND siteid = ?
						'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_base_coupon" ( :names
							"baseid", "ordprodid", "code", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_base_coupon"
						SET :names
							"baseid" = ?, "ordprodid" = ?, "code" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordbaco."id" AS "order.base.coupon.id", mordbaco."baseid" AS "order.base.coupon.baseid",
							mordbaco."siteid" AS "order.base.coupon.siteid", mordbaco."ordprodid" AS "order.base.coupon.ordprodid",
							mordbaco."code" AS "order.base.coupon.code", mordbaco."mtime" AS "order.base.coupon.mtime",
							mordbaco."editor" AS "order.base.coupon.editor", mordbaco."ctime" AS "order.base.coupon.ctime"
						FROM "mshop_order_base_coupon" AS mordbaco
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordbaco."id" AS "order.base.coupon.id", mordbaco."baseid" AS "order.base.coupon.baseid",
							mordbaco."siteid" AS "order.base.coupon.siteid", mordbaco."ordprodid" AS "order.base.coupon.ordprodid",
							mordbaco."code" AS "order.base.coupon.code", mordbaco."mtime" AS "order.base.coupon.mtime",
							mordbaco."editor" AS "order.base.coupon.editor", mordbaco."ctime" AS "order.base.coupon.ctime"
						FROM "mshop_order_base_coupon" AS mordbaco
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordbaco."id" ) AS "count"
						FROM "mshop_order_base_coupon" AS mordbaco
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_base_coupon_seq.CURRVAL FROM DUAL',
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
								SELECT :acols, :val AS "val"
								FROM "mshop_order_base_product_attr" AS mordbaprat
								:joins
								WHERE :cond
								GROUP BY mordbaprat.id, :cols, :val
								ORDER BY mordbaprat.id DESC
								OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
							) AS list
							GROUP BY :keys
						',
						'mysql' => '
							SELECT :keys, :type("val") AS "value"
							FROM (
								SELECT :acols, :val AS "val"
								FROM "mshop_order_base_product_attr" AS mordbaprat
								:joins
								WHERE :cond
								GROUP BY mordbaprat.id, :cols, :val
								ORDER BY mordbaprat.id DESC
								LIMIT :size OFFSET :start
							) AS list
							GROUP BY :keys
						'
					),
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_order_base_product_attr"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_order_base_product_attr" ( :names
								"attrid", "parentid", "type", "code", "value",
								"quantity", "name", "mtime", "editor", "siteid", "ctime"
							) VALUES ( :values
								?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_order_base_product_attr"
							SET :names
								"attrid" = ?, "parentid" = ?, "type" = ?, "code" = ?,
								"value" = ?, "quantity" = ?, "name" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT :columns
								mordbaprat."id" AS "order.base.product.attribute.id", mordbaprat."siteid" AS "order.base.product.attribute.siteid",
								mordbaprat."attrid" AS "order.base.product.attribute.attributeid", mordbaprat."parentid" AS "order.base.product.attribute.parentid",
								mordbaprat."type" AS "order.base.product.attribute.type", mordbaprat."code" AS "order.base.product.attribute.code",
								mordbaprat."value" AS "order.base.product.attribute.value", mordbaprat."quantity" AS "order.base.product.attribute.quantity",
								mordbaprat."name" AS "order.base.product.attribute.name", mordbaprat."mtime" AS "order.base.product.attribute.mtime",
								mordbaprat."editor" AS "order.base.product.attribute.editor", mordbaprat."ctime" AS "order.base.product.attribute.ctime"
							FROM "mshop_order_base_product_attr" AS mordbaprat
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mordbaprat."id" AS "order.base.product.attribute.id", mordbaprat."siteid" AS "order.base.product.attribute.siteid",
								mordbaprat."attrid" AS "order.base.product.attribute.attributeid", mordbaprat."parentid" AS "order.base.product.attribute.parentid",
								mordbaprat."type" AS "order.base.product.attribute.type", mordbaprat."code" AS "order.base.product.attribute.code",
								mordbaprat."value" AS "order.base.product.attribute.value", mordbaprat."quantity" AS "order.base.product.attribute.quantity",
								mordbaprat."name" AS "order.base.product.attribute.name", mordbaprat."mtime" AS "order.base.product.attribute.mtime",
								mordbaprat."editor" AS "order.base.product.attribute.editor", mordbaprat."ctime" AS "order.base.product.attribute.ctime"
							FROM "mshop_order_base_product_attr" AS mordbaprat
							:joins
							WHERE :cond
							ORDER BY :order
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT( DISTINCT mordbaprat."id" ) AS "count"
							FROM "mshop_order_base_product_attr" AS mordbaprat
							:joins
							WHERE :cond
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_order_base_product_attr_seq.CURRVAL FROM DUAL',
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
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_product" AS mordbapr
							:joins
							WHERE :cond
							GROUP BY mordbapr.id, :cols, :val
							ORDER BY mordbapr.id DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_product" AS mordbapr
							:joins
							WHERE :cond
							GROUP BY mordbapr.id, :cols, :val
							ORDER BY mordbapr.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_base_product"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_base_product" ( :names
							"baseid", "ordprodid", "ordaddrid", "type", "parentprodid", "prodid", "prodcode", "supplierid",
							"suppliername", "stocktype", "name", "description", "mediaurl", "timeframe",
							"quantity", "currencyid", "price", "costs", "rebate", "tax", "taxrate", "taxflag",
							"flags", "statuspayment", "statusdelivery", "pos", "mtime", "editor", "target", "qtyopen", "notes",
							"siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_order_base_product"
						SET :names
							"baseid" = ?, "ordprodid" = ?, "ordaddrid" = ?, "type" = ?, "parentprodid" = ?,
							"prodid" = ?, "prodcode" = ?, "supplierid" = ?, "suppliername" = ?, "stocktype" = ?,
							"name" = ?, "description" = ?, "mediaurl" = ?, "timeframe" = ?, "quantity" = ?,
							"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?,
							"taxflag" = ?, "flags" = ?, "statuspayment" = ?, "statusdelivery" = ?, "pos" = ?,
							"mtime" = ?, "editor" = ?, "target" = ?, "qtyopen" = ?, "notes" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordbapr."id" AS "order.base.product.id", mordbapr."baseid" AS "order.base.product.baseid",
							mordbapr."siteid" AS "order.base.product.siteid", mordbapr."ordprodid" AS "order.base.product.orderproductid",
							mordbapr."prodid" AS "order.base.product.productid", mordbapr."prodcode" AS "order.base.product.prodcode",
							mordbapr."description" AS "order.base.product.description", mordbapr."stocktype" AS "order.base.product.stocktype",
							mordbapr."type" AS "order.base.product.type", mordbapr."name" AS "order.base.product.name",
							mordbapr."mediaurl" AS "order.base.product.mediaurl", mordbapr."timeframe" AS "order.base.product.timeframe",
							mordbapr."quantity" AS "order.base.product.quantity", mordbapr."currencyid" AS "order.base.product.currencyid",
							mordbapr."price" AS "order.base.product.price", mordbapr."costs" AS "order.base.product.costs",
							mordbapr."rebate" AS "order.base.product.rebate", mordbapr."tax" AS "order.base.product.taxvalue",
							mordbapr."taxrate" AS "order.base.product.taxrates", mordbapr."taxflag" AS "order.base.product.taxflag",
							mordbapr."flags" AS "order.base.product.flags", mordbapr."statusdelivery" AS "order.base.product.statusdelivery",
							mordbapr."pos" AS "order.base.product.position", mordbapr."mtime" AS "order.base.product.mtime",
							mordbapr."editor" AS "order.base.product.editor", mordbapr."ctime" AS "order.base.product.ctime",
							mordbapr."target" AS "order.base.product.target", mordbapr."ordaddrid" AS "order.base.product.orderaddressid",
							mordbapr."supplierid" AS "order.base.product.supplierid", mordbapr."suppliername" AS "order.base.product.suppliername",
							mordbapr."qtyopen" AS "order.base.product.qtyopen", mordbapr."notes" AS "order.base.product.notes",
							mordbapr."statuspayment" AS "order.base.product.statuspayment", mordbapr."parentprodid" AS "order.base.product.parentproductid"
						FROM "mshop_order_base_product" AS mordbapr
						:joins
						WHERE :cond
						GROUP BY :columns :group
							mordbapr."id", mordbapr."baseid", mordbapr."siteid", mordbapr."ordprodid", mordbapr."prodid",
							mordbapr."prodcode", mordbapr."description", mordbapr."stocktype", mordbapr."type",
							mordbapr."name", mordbapr."mediaurl", mordbapr."timeframe", mordbapr."quantity",
							mordbapr."currencyid", mordbapr."price", mordbapr."costs", mordbapr."rebate", mordbapr."tax",
							mordbapr."taxrate", mordbapr."taxflag", mordbapr."flags", mordbapr."statusdelivery", mordbapr."pos",
							mordbapr."mtime", mordbapr."editor", mordbapr."ctime", mordbapr."target", mordbapr."ordaddrid",
							mordbapr."supplierid", mordbapr."suppliername", mordbapr."qtyopen", mordbapr."notes",
							mordbapr."statuspayment", mordbapr."parentprodid"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordbapr."id" AS "order.base.product.id", mordbapr."baseid" AS "order.base.product.baseid",
							mordbapr."siteid" AS "order.base.product.siteid", mordbapr."ordprodid" AS "order.base.product.orderproductid",
							mordbapr."prodid" AS "order.base.product.productid", mordbapr."prodcode" AS "order.base.product.prodcode",
							mordbapr."description" AS "order.base.product.description", mordbapr."stocktype" AS "order.base.product.stocktype",
							mordbapr."type" AS "order.base.product.type", mordbapr."name" AS "order.base.product.name",
							mordbapr."mediaurl" AS "order.base.product.mediaurl", mordbapr."timeframe" AS "order.base.product.timeframe",
							mordbapr."quantity" AS "order.base.product.quantity", mordbapr."currencyid" AS "order.base.product.currencyid",
							mordbapr."price" AS "order.base.product.price", mordbapr."costs" AS "order.base.product.costs",
							mordbapr."rebate" AS "order.base.product.rebate", mordbapr."tax" AS "order.base.product.taxvalue",
							mordbapr."taxrate" AS "order.base.product.taxrates", mordbapr."taxflag" AS "order.base.product.taxflag",
							mordbapr."flags" AS "order.base.product.flags", mordbapr."statusdelivery" AS "order.base.product.statusdelivery",
							mordbapr."pos" AS "order.base.product.position", mordbapr."mtime" AS "order.base.product.mtime",
							mordbapr."editor" AS "order.base.product.editor", mordbapr."ctime" AS "order.base.product.ctime",
							mordbapr."target" AS "order.base.product.target", mordbapr."ordaddrid" AS "order.base.product.orderaddressid",
							mordbapr."supplierid" AS "order.base.product.supplierid", mordbapr."suppliername" AS "order.base.product.suppliername",
							mordbapr."qtyopen" AS "order.base.product.qtyopen", mordbapr."notes" AS "order.base.product.notes",
							mordbapr."statuspayment" AS "order.base.product.statuspayment", mordbapr."parentprodid" AS "order.base.product.parentproductid"
						FROM "mshop_order_base_product" AS mordbapr
						:joins
						WHERE :cond
						GROUP BY :group mordbapr."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordbapr."id" ) AS "count"
						FROM "mshop_order_base_product" AS mordbapr
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_base_product_seq.CURRVAL FROM DUAL',
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
								SELECT :acols, :val AS "val"
								FROM "mshop_order_base_service_attr" AS mordbaseat
								:joins
								WHERE :cond
								GROUP BY mordbaseat.id, :cols, :val
								ORDER BY mordbaseat.id DESC
								OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
							) AS list
							GROUP BY :keys
						',
						'mysql' => '
							SELECT :keys, :type("val") AS "value"
							FROM (
								SELECT :acols, :val AS "val"
								FROM "mshop_order_base_service_attr" AS mordbaseat
								:joins
								WHERE :cond
								GROUP BY mordbaseat.id, :cols, :val
								ORDER BY mordbaseat.id DESC
								LIMIT :size OFFSET :start
							) AS list
							GROUP BY :keys
						'
					),
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_order_base_service_attr"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_order_base_service_attr" ( :names
								"attrid", "parentid", "type", "code", "value",
								"quantity", "name", "mtime", "editor", "siteid", "ctime"
							) VALUES ( :values
								?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_order_base_service_attr"
							SET :names
								"attrid" = ?, "parentid" = ?, "type" = ?, "code" = ?,
								"value" = ?, "quantity" = ?, "name" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT :columns
								mordbaseat."id" AS "order.base.service.attribute.id", mordbaseat."siteid" AS "order.base.service.attribute.siteid",
								mordbaseat."attrid" AS "order.base.service.attribute.attributeid", mordbaseat."parentid" AS "order.base.service.attribute.parentid",
								mordbaseat."type" AS "order.base.service.attribute.type", mordbaseat."code" AS "order.base.service.attribute.code",
								mordbaseat."value" AS "order.base.service.attribute.value", mordbaseat."quantity" AS "order.base.service.attribute.quantity",
								mordbaseat."name" AS "order.base.service.attribute.name", mordbaseat."mtime" AS "order.base.service.attribute.mtime",
								mordbaseat."ctime" AS "order.base.service.attribute.ctime", mordbaseat."editor" AS "order.base.service.attribute.editor"
							FROM "mshop_order_base_service_attr" AS mordbaseat
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mordbaseat."id" AS "order.base.service.attribute.id", mordbaseat."siteid" AS "order.base.service.attribute.siteid",
								mordbaseat."attrid" AS "order.base.service.attribute.attributeid", mordbaseat."parentid" AS "order.base.service.attribute.parentid",
								mordbaseat."type" AS "order.base.service.attribute.type", mordbaseat."code" AS "order.base.service.attribute.code",
								mordbaseat."value" AS "order.base.service.attribute.value", mordbaseat."quantity" AS "order.base.service.attribute.quantity",
								mordbaseat."name" AS "order.base.service.attribute.name", mordbaseat."mtime" AS "order.base.service.attribute.mtime",
								mordbaseat."ctime" AS "order.base.service.attribute.ctime", mordbaseat."editor" AS "order.base.service.attribute.editor"
							FROM "mshop_order_base_service_attr" AS mordbaseat
							:joins
							WHERE :cond
							ORDER BY :order
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT( DISTINCT mordbaseat."id" ) AS "count"
							FROM "mshop_order_base_service_attr" AS mordbaseat
							:joins
							WHERE :cond
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_order_base_service_attr_seq.CURRVAL FROM DUAL',
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
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_service" AS mordbase
							:joins
							WHERE :cond
							GROUP BY mordbase.id, :cols, :val
							ORDER BY mordbase.id DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, :type("val") AS "value"
						FROM (
							SELECT :acols, :val AS "val"
							FROM "mshop_order_base_service" AS mordbase
							:joins
							WHERE :cond
							GROUP BY mordbase.id, :cols, :val
							ORDER BY mordbase.id DESC
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_order_base_service"
						WHERE :cond AND siteid = ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_order_base_service" ( :names
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
						UPDATE "mshop_order_base_service"
						SET :names
							"baseid" = ?, "servid" = ?, "type" = ?, "code" = ?,
							"name" = ?, "mediaurl" = ?, "currencyid" = ?, "price" = ?,
							"costs" = ?, "rebate" = ?, "tax" = ?, "taxrate" = ?,
							"taxflag" = ?, "pos" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mordbase."id" AS "order.base.service.id", mordbase."baseid" AS "order.base.service.baseid",
							mordbase."siteid" AS "order.base.service.siteid", mordbase."servid" AS "order.base.service.serviceid",
							mordbase."type" AS "order.base.service.type", mordbase."code" AS "order.base.service.code",
							mordbase."name" AS "order.base.service.name", mordbase."mediaurl" AS "order.base.service.mediaurl",
							mordbase."currencyid" AS "order.base.service.currencyid", mordbase."price" AS "order.base.service.price",
							mordbase."costs" AS "order.base.service.costs", mordbase."rebate" AS "order.base.service.rebate",
							mordbase."tax" AS "order.base.service.taxvalue", mordbase."taxrate" AS "order.base.service.taxrates",
							mordbase."taxflag" AS "order.base.service.taxflag", mordbase."pos" AS "order.base.service.position",
							mordbase."mtime" AS "order.base.service.mtime", mordbase."editor" AS "order.base.service.editor",
							mordbase."ctime" AS "order.base.service.ctime"
						FROM "mshop_order_base_service" AS mordbase
						:joins
						WHERE :cond
						GROUP BY :columns :group
							mordbase."id", mordbase."baseid", mordbase."siteid", mordbase."servid", mordbase."type",
							mordbase."code", mordbase."name", mordbase."mediaurl", mordbase."currencyid", mordbase."price",
							mordbase."costs", mordbase."rebate", mordbase."tax", mordbase."taxrate", mordbase."taxflag",
							mordbase."pos", mordbase."mtime", mordbase."editor", mordbase."ctime"
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mordbase."id" AS "order.base.service.id", mordbase."baseid" AS "order.base.service.baseid",
							mordbase."siteid" AS "order.base.service.siteid", mordbase."servid" AS "order.base.service.serviceid",
							mordbase."type" AS "order.base.service.type", mordbase."code" AS "order.base.service.code",
							mordbase."name" AS "order.base.service.name", mordbase."mediaurl" AS "order.base.service.mediaurl",
							mordbase."currencyid" AS "order.base.service.currencyid", mordbase."price" AS "order.base.service.price",
							mordbase."costs" AS "order.base.service.costs", mordbase."rebate" AS "order.base.service.rebate",
							mordbase."tax" AS "order.base.service.taxvalue", mordbase."taxrate" AS "order.base.service.taxrates",
							mordbase."taxflag" AS "order.base.service.taxflag", mordbase."pos" AS "order.base.service.position",
							mordbase."mtime" AS "order.base.service.mtime", mordbase."editor" AS "order.base.service.editor",
							mordbase."ctime" AS "order.base.service.ctime"
						FROM "mshop_order_base_service" AS mordbase
						:joins
						WHERE :cond
						GROUP BY :group mordbase."id"
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT( DISTINCT mordbase."id" ) AS "count"
						FROM "mshop_order_base_service" AS mordbase
						:joins
						WHERE :cond
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_order_base_service_seq.CURRVAL FROM DUAL',
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
						SELECT :acols, :val AS "val"
						FROM "mshop_order_base" AS mordba
						:joins
						WHERE :cond
						GROUP BY mordba.id, :cols, :val
						ORDER BY mordba.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_order_base" AS mordba
						:joins
						WHERE :cond
						GROUP BY mordba.id, :cols, :val
						ORDER BY mordba.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_base"
					WHERE :cond AND siteid = ?
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
					WHERE "siteid" = ? AND "id" = ?
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
					FROM "mshop_order_base" AS mordba
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
					FROM "mshop_order_base" AS mordba
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
					FROM "mshop_order_base" AS mordba
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
		'status' => array(
			'aggregate' => array(
				'ansi' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_order_status" AS mordst
						:joins
						WHERE :cond
						GROUP BY mordst.id, :cols, :val
						ORDER BY mordst.id DESC
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					) AS list
					GROUP BY :keys
				',
				'mysql' => '
					SELECT :keys, :type("val") AS "value"
					FROM (
						SELECT :acols, :val AS "val"
						FROM "mshop_order_status" AS mordst
						:joins
						WHERE :cond
						GROUP BY mordst.id, :cols, :val
						ORDER BY mordst.id DESC
						LIMIT :size OFFSET :start
					) AS list
					GROUP BY :keys
				'
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_order_status"
					WHERE :cond AND siteid = ?
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
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mordst."id" AS "order.status.id", mordst."siteid" AS "order.status.siteid",
						mordst."parentid" AS "order.status.parentid", mordst."type" AS "order.status.type",
						mordst."value" AS "order.status.value", mordst."mtime" AS "order.status.mtime",
						mordst."ctime" AS "order.status.ctime", mordst."editor" AS "order.status.editor"
					FROM "mshop_order_status" AS mordst
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
					FROM "mshop_order_status" AS mordst
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT( DISTINCT mordst."id" ) AS "count"
					FROM "mshop_order_status" AS mordst
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
					SELECT :acols, :val AS "val"
					FROM "mshop_order" AS mord
					:joins
					WHERE :cond
					GROUP BY mord.id, :cols, :val
					ORDER BY mord.id DESC
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				) AS list
				GROUP BY :keys
			',
			'mysql' => '
				SELECT :keys, :type("val") AS "value"
				FROM (
					SELECT :acols, :val AS "val"
					FROM "mshop_order" AS mord
					:joins
					WHERE :cond
					GROUP BY mord.id, :cols, :val
					ORDER BY mord.id DESC
					LIMIT :size OFFSET :start
				) AS list
				GROUP BY :keys
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_order" ( :names
					"baseid", "type", "datepayment", "datedelivery",
					"statusdelivery", "statuspayment", "relatedid", "mtime",
					"editor", "siteid", "ctime", "cdate", "cmonth", "cweek", "cwday", "chour"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_order"
				SET :names
					"baseid" = ?, "type" = ?, "datepayment" = ?, "datedelivery" = ?, "statusdelivery" = ?,
					"statuspayment" = ?, "relatedid" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_order"
				WHERE :cond AND siteid = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					mord."id" AS "order.id", mord."baseid" AS "order.baseid",
					mord."siteid" AS "order.siteid", mord."type" AS "order.type",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid", mord."ctime" AS "order.ctime",
					mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
				FROM "mshop_order" AS mord
				:joins
				WHERE :cond
				GROUP BY :columns :group
					mord."id", mord."baseid", mord."siteid", mord."type", mord."datepayment", mord."datedelivery",
					mord."statuspayment", mord."statusdelivery", mord."relatedid", mord."ctime", mord."mtime", mord."editor"
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					mord."id" AS "order.id", mord."baseid" AS "order.baseid",
					mord."siteid" AS "order.siteid", mord."type" AS "order.type",
					mord."datepayment" AS "order.datepayment", mord."datedelivery" AS "order.datedelivery",
					mord."statuspayment" AS "order.statuspayment", mord."statusdelivery" AS "order.statusdelivery",
					mord."relatedid" AS "order.relatedid", mord."ctime" AS "order.ctime",
					mord."mtime" AS "order.mtime", mord."editor" AS "order.editor"
				FROM "mshop_order" AS mord
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
				FROM "mshop_order" AS mord
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
