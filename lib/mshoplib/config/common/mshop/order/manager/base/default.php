<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base" (
				"siteid", "customerid", "sitecode", "langid", "currencyid",
				"price", "costs", "rebate", "comment", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_order_base"
			SET "siteid" = ?, "customerid" = ?, "sitecode" = ?, "langid" = ?,
				"currencyid" = ?, "price" = ?, "costs" = ?, "rebate" = ?,
				"comment" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mordba."id", mordba."siteid", mordba."sitecode",
				mordba."customerid", mordba."langid", mordba."currencyid",
				mordba."price", mordba."costs", mordba."rebate",
				mordba."comment", mordba."status", mordba."mtime",
				mordba."editor", mordba."ctime"
			FROM "mshop_order_base" AS mordba
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordba."id"
				FROM "mshop_order_base" AS mordba
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
