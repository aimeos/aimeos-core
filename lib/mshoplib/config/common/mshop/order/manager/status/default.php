<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: default.php 14389 2011-12-16 14:31:10Z doleiynyk $
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
			WHERE "id" = ?
		',
		'search' => '
			SELECT mordst."id", mordst."siteid", mordst."parentid", mordst."type", mordst."value",
				mordst."mtime", mordst."ctime", mordst."editor"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
			/*-orderby*/ORDER BY :order/*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT( mordst."id" ) AS "count"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
		',
	),
);
