<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order_status" ("siteid", "parentid", "type",
				"value", "mtime", "editor", "ctime" )
			VALUES (?, ?, ?, ?, ?, ?, ?)
		',
		'update' => '
			UPDATE "mshop_order_status"
			SET "siteid" = ?, "parentid" = ?, "type" = ?,
				"value" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_order_status"
			WHERE :cond
			AND siteid = ?
		',
		'search' => '
			SELECT DISTINCT mordst."id", mordst."siteid", mordst."parentid", mordst."type", mordst."value",
				mordst."mtime", mordst."ctime", mordst."editor"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
			/*-orderby*/ORDER BY :order/*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mordst."id"
				FROM "mshop_order_status" AS mordst
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
