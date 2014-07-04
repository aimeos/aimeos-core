<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'item' => array(
		'delete' => '
			DELETE FROM "mshop_order_base_service_attr"
			WHERE :cond AND siteid = ?
		',
		'insert' => '
			INSERT INTO "mshop_order_base_service_attr" (
				"siteid", "attrid", "ordservid", "type", "code", "value",
				"name", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_order_base_service_attr"
			SET "siteid" = ?, "attrid" = ?, "ordservid" = ?, "type" = ?,
				"code" = ?, "value" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'search' => '
			SELECT DISTINCT mordbaseat."id", mordbaseat."siteid",
				mordbaseat."attrid", mordbaseat."ordservid", mordbaseat."type",
				mordbaseat."code", mordbaseat."value", mordbaseat."name",
				mordbaseat."mtime", mordbaseat."ctime", mordbaseat."editor"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mordbaseat."id"
				FROM "mshop_order_base_service_attr" AS mordbaseat
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);