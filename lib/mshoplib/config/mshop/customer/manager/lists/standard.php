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
				SELECT :key AS "key", mcusli."id" AS "id"
				FROM "mshop_customer_list" AS mcusli
				:joins
				WHERE :cond
				GROUP BY :key, mcusli."id"
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_customer_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_customer_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_customer_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcusli."id" AS "customer.lists.id", mcusli."parentid" AS "customer.lists.parentid",
				mcusli."siteid" AS "customer.lists.siteid", mcusli."typeid" AS "customer.lists.typeid",
				mcusli."domain" AS "customer.lists.domain", mcusli."refid" AS "customer.lists.refid",
				mcusli."start" AS "customer.lists.datestart", mcusli."end" AS "customer.lists.dateend",
				mcusli."config" AS "customer.lists.config", mcusli."pos" AS "customer.lists.position",
				mcusli."status" AS "customer.lists.status", mcusli."mtime" AS "customer.lists.mtime",
				mcusli."editor" AS "customer.lists.editor", mcusli."ctime" AS "customer.lists.ctime"
			FROM "mshop_customer_list" AS mcusli
			:joins
			WHERE :cond
			GROUP BY mcusli."id", mcusli."parentid", mcusli."siteid", mcusli."typeid",
				mcusli."domain", mcusli."refid", mcusli."start", mcusli."end",
				mcusli."config", mcusli."pos", mcusli."status", mcusli."mtime",
				mcusli."editor", mcusli."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcusli."id"
				FROM "mshop_customer_list" AS mcusli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_customer_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

