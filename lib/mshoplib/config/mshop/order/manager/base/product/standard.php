<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbapr."id" AS "id"
			FROM "mshop_order_base_product" AS mordbapr
			:joins
			WHERE :cond
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
				"suppliercode", "warehousecode", "name", "mediaurl",
				"quantity", "price", "costs", "rebate", "taxrate", "flags",
				"status", "pos", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_product"
			SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "type" = ?,
				"prodid" = ?, "prodcode" = ?, "suppliercode" = ?,
				"warehousecode" = ?, "name" = ?, "mediaurl" = ?,
				"quantity" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
				"taxrate" = ?, "flags" = ?, "status" = ?, "pos" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordbapr."id", mordbapr."baseid",
				mordbapr."siteid", mordbapr."ordprodid", mordbapr."type",
				mordbapr."prodid", mordbapr."prodcode",
				mordbapr."suppliercode", mordbapr."warehousecode",
				mordbapr."name", mordbapr."mediaurl", mordbapr."quantity",
				mordbapr."price", mordbapr."costs", mordbapr."rebate",
				mordbapr."taxrate", mordbapr."flags", mordbapr."status",
				mordbapr."mtime", mordbapr."pos", mordbapr."editor",
				mordbapr."ctime"
			FROM "mshop_order_base_product" AS mordbapr
			:joins
			WHERE :cond
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
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

