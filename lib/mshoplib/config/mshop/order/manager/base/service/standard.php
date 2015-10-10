<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbase."id" AS "id"
			FROM "mshop_order_base_service" AS mordbase
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
			DELETE FROM "mshop_order_base_service"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_service" (
				"baseid", "siteid", "servid", "type", "code", "name",
				"mediaurl", "price", "costs", "rebate", "taxrate", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_service"
			SET "baseid" = ?, "siteid" = ?, "servid" = ?, "type" = ?,
				"code" = ?, "name" = ?, "mediaurl" = ?, "price" = ?,
				"costs" = ?, "rebate" = ?, "taxrate" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordbase."id", mordbase."baseid",
				mordbase."siteid", mordbase."servid", mordbase."type",
				mordbase."code", mordbase."name", mordbase."mediaurl",
				mordbase."price", mordbase."costs", mordbase."rebate",
				mordbase."taxrate", mordbase."mtime", mordbase."editor",
				mordbase."ctime"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbase."id" ) AS "count"
			FROM "mshop_order_base_service" AS mordbase
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);
