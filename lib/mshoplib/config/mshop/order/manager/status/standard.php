<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT :key AS "key", mordst."id" AS "id"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
			GROUP BY :key, mordst."id" /*-columns*/ , :columns /*columns-*/
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
			SELECT mordst."id" AS "order.status.id", mordst."siteid" AS "order.status.siteid",
				mordst."parentid" AS "order.status.parentid", mordst."type" AS "order.status.type",
				mordst."value" AS "order.status.value", mordst."mtime" AS "order.status.mtime",
				mordst."ctime" AS "order.status.ctime", mordst."editor" AS "order.status.editor"
			FROM "mshop_order_status" AS mordst
			:joins
			WHERE :cond
			GROUP BY mordst."id", mordst."siteid", mordst."parentid", mordst."type",
				mordst."value", mordst."mtime", mordst."ctime", mordst."editor"
				/*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_order_status_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

