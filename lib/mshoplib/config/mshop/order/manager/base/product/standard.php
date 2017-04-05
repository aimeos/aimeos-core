<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordbapr."id" AS "id"
			FROM "mshop_order_base_product" AS mordbapr
			:joins
			WHERE :cond
			GROUP BY :key, mordbapr."id" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
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
			INSERT INTO "mshop_order_base_product" (
				"baseid", "siteid", "ordprodid", "type", "prodid", "prodcode",
				"suppliercode", "stocktype", "name", "mediaurl", "quantity",
				"price", "costs", "rebate", "tax", "taxrate", "taxflag", "flags",
				"status", "pos", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_product"
			SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "type" = ?,
				"prodid" = ?, "prodcode" = ?, "suppliercode" = ?,
				"stocktype" = ?, "name" = ?, "mediaurl" = ?,
				"quantity" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
				"tax" = ?, "taxrate" = ?, "taxflag" = ?, "flags" = ?,
				"status" = ?, "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mordbapr."id" AS "order.base.product.id", mordbapr."baseid" AS "order.base.product.baseid",
				mordbapr."siteid" AS "order.base.product.siteid", mordbapr."ordprodid" AS "order.base.product.ordprodid",
				mordbapr."prodid" AS "order.base.product.productid", mordbapr."prodcode" AS "order.base.product.prodcode",
				mordbapr."suppliercode" AS "order.base.product.suppliercode", mordbapr."stocktype" AS "order.base.product.stocktype",
				mordbapr."type" AS "order.base.product.type", mordbapr."name" AS "order.base.product.name",
				mordbapr."mediaurl" AS "order.base.product.mediaurl", mordbapr."quantity" AS "order.base.product.quantity",
				mordbapr."price" AS "order.base.product.price", mordbapr."costs" AS "order.base.product.costs",
				mordbapr."rebate" AS "order.base.product.rebate", mordbapr."tax" AS "order.base.product.taxvalue",
				mordbapr."taxrate" AS "order.base.product.taxrate", mordbapr."taxflag" AS "order.base.product.taxflag",
				mordbapr."flags" AS "order.base.product.flags", mordbapr."status" AS "order.base.product.status",
				mordbapr."pos" AS "order.base.product.position", mordbapr."mtime" AS "order.base.product.mtime",
				mordbapr."editor" AS "order.base.product.editor", mordbapr."ctime" AS "order.base.product.ctime"
			FROM "mshop_order_base_product" AS mordbapr
			:joins
			WHERE :cond
			GROUP BY mordbapr."id", mordbapr."baseid", mordbapr."siteid", mordbapr."ordprodid",
				mordbapr."prodid", mordbapr."prodcode", mordbapr."suppliercode", mordbapr."stocktype",
				mordbapr."type", mordbapr."name", mordbapr."mediaurl", mordbapr."quantity",
				mordbapr."price", mordbapr."costs", mordbapr."rebate", mordbapr."tax", mordbapr."taxrate",
				mordbapr."taxflag", mordbapr."flags", mordbapr."status", mordbapr."pos", mordbapr."mtime",
				mordbapr."editor", mordbapr."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
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
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

