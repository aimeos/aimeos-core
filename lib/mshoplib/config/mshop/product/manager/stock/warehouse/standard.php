<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_stock_warehouse"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_stock_warehouse" (
				"siteid", "code", "label", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_stock_warehouse"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mprostwa."id" AS "product.stock.warehouse.id", mprostwa."siteid" AS "product.stock.warehouse.siteid",
				mprostwa."code" AS "product.stock.warehouse.code", mprostwa."label" AS "product.stock.warehouse.label",
				mprostwa."status" AS "product.stock.warehouse.status", mprostwa."mtime" AS "product.stock.warehouse.mtime",
				mprostwa."editor" AS "product.stock.warehouse.editor", mprostwa."ctime" AS "product.stock.warehouse.ctime"
			FROM "mshop_product_stock_warehouse" AS mprostwa
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprostwa."id"
				FROM "mshop_product_stock_warehouse" AS mprostwa
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

