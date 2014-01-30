<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order_base_product" ("baseid", "siteid", "ordprodid", "type", "prodid", "prodcode", "suppliercode", "name",
				"mediaurl", "quantity", "price", "costs", "rebate", "taxrate", "flags", "status", "pos",
				"mtime", "editor", "ctime")
			VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		',
		'update' => '
			UPDATE "mshop_order_base_product"
			SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "type" = ?, "prodid" = ?, "prodcode" = ?, "suppliercode" = ?, "name" = ?, "mediaurl" = ?,
				"quantity" = ?, "price" = ?, "costs" = ?, "rebate" = ?, "taxrate" = ?, "flags" = ?, "status" = ?, "pos" = ?,
				"mtime" = ? , "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_order_base_product"
			WHERE :cond
			AND siteid = ?
		',
		'search' => '
			SELECT mordbapr."id", mordbapr."baseid", mordbapr."siteid", mordbapr."ordprodid", mordbapr."type", mordbapr."prodid", mordbapr."prodcode",
				mordbapr."suppliercode", mordbapr."name", mordbapr."mediaurl", mordbapr."quantity", mordbapr."price",
				mordbapr."costs", mordbapr."rebate", mordbapr."taxrate", mordbapr."flags", mordbapr."status",
				mordbapr."mtime", mordbapr."pos", mordbapr."editor", mordbapr."ctime"
			FROM "mshop_order_base_product" AS mordbapr
			:joins
			WHERE :cond
			/*-groupby*/ GROUP BY mordbapr."id", mordbapr."baseid", mordbapr."siteid", mordbapr."ordprodid", mordbapr."type", mordbapr."prodcode", mordbapr."suppliercode",
				mordbapr."name", mordbapr."mediaurl", mordbapr."quantity", mordbapr."price", mordbapr."costs",
				mordbapr."rebate", mordbapr."taxrate", mordbapr."flags", mordbapr."status",
				mordbapr."mtime", mordbapr."pos", mordbapr."editor", mordbapr."ctime" /*groupby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :start, :size
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
