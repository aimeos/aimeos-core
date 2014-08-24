<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

return array(
	'aggregate' => '
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
	',
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_product"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_product" (
				"baseid", "siteid", "ordprodid", "type", "prodid", "prodcode",
				"suppliercode", "warehousecode", "name", "mediaurl",
				"quantity", "price", "costs", "rebate", "taxrate", "flags",
				"status", "pos", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_order_base_product"
			SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "type" = ?,
				"prodid" = ?, "prodcode" = ?, "suppliercode" = ?,
				"warehousecode" = ?, "name" = ?, "mediaurl" = ?,
				"quantity" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
				"taxrate" = ?, "flags" = ?, "status" = ?, "pos" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
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
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordbapr."id"
				FROM "mshop_order_base_product" AS mordbapr
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
