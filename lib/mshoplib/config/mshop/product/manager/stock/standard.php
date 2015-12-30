<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_stock"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_stock" (
				"parentid", "siteid", "warehouseid", "stocklevel", "backdate",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_stock"
			SET "parentid" = ?, "siteid" = ?, "warehouseid" = ?,
				"stocklevel" = ?, "backdate" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mprost."id" AS "product.stock.id", mprost."parentid" AS "product.stock.parentid",
				mprost."siteid" AS "product.stock.siteid", mprost."warehouseid" AS "product.stock.warehouseid",
				mprost."stocklevel" AS "product.stock.stocklevel", mprost."backdate" AS "product.stock.backdate",
				mprost."mtime" AS "product.stock.mtime", mprost."editor" AS "product.stock.editor",
				mprost."ctime" AS "product.stock.ctime"
			FROM "mshop_product_stock" AS mprost
			:joins
			WHERE :cond
			GROUP BY mprost."id", mprost."parentid", mprost."siteid", mprost."warehouseid",
				mprost."stocklevel", mprost."backdate", mprost."mtime", mprost."editor",
				mprost."ctime" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprost."id"
				FROM "mshop_product_stock" AS mprost
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'stocklevel' => array(
		'ansi' => '
			UPDATE "mshop_product_stock"
			SET "stocklevel" = "stocklevel" + ?, "mtime" = ?, "editor" = ?
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

