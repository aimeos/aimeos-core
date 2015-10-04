<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'aggregate' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbaseat."id" AS "id"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	',
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
			SELECT COUNT( DISTINCT mordbaseat."id" ) AS "count"
			FROM "mshop_order_base_service_attr" AS mordbaseat
			:joins
			WHERE :cond
		',
	),
);