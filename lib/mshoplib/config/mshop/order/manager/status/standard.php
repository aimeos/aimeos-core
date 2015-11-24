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
			SELECT DISTINCT :key AS "key", mordst."id" AS "id"
			FROM "mshop_order_status" AS mordst
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
			DELETE FROM "mshop_order_status"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_status" (
				"siteid", "parentid", "type", "value", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_status"
			SET "siteid" = ?, "parentid" = ?, "type" = ?, "value" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordst."id" AS "order.status.id", mordst."siteid" AS "order.status.siteid",
				mordst."parentid" AS "order.status.parentid", mordst."type" AS "order.status.type",
				mordst."value" AS "order.status.value", mordst."mtime" AS "order.status.mtime",
				mordst."ctime" AS "order.status.ctime", mordst."editor" AS "order.status.editor"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordst."id" ) AS "count"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

