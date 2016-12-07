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
				SELECT :key AS "key", mcatli."id" AS "id"
				FROM "mshop_catalog_list" AS mcatli
				:joins
				WHERE :cond
				GROUP BY :key, mcatli."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_catalog_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_catalog_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_catalog_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_catalog_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_catalog_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_catalog_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcatli."id" AS "catalog.lists.id", mcatli."parentid" AS "catalog.lists.parentid",
				mcatli."siteid" AS "catalog.lists.siteid", mcatli."typeid" AS "catalog.lists.typeid",
				mcatli."domain" AS "catalog.lists.domain", mcatli."refid" AS "catalog.lists.refid",
				mcatli."start" AS "catalog.lists.datestart", mcatli."end" AS "catalog.lists.dateend",
				mcatli."config" AS "catalog.lists.config", mcatli."pos" AS "catalog.lists.position",
				mcatli."status" AS "catalog.lists.status", mcatli."mtime" AS "catalog.lists.mtime",
				mcatli."editor" AS "catalog.lists.editor", mcatli."ctime" AS "catalog.lists.ctime"
			FROM "mshop_catalog_list" AS mcatli
			:joins
			WHERE :cond
			GROUP BY mcatli."id", mcatli."parentid", mcatli."siteid", mcatli."typeid",
				mcatli."domain", mcatli."refid", mcatli."start", mcatli."end",
				mcatli."config", mcatli."pos", mcatli."status", mcatli."mtime",
				mcatli."editor", mcatli."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcatli."id"
				FROM "mshop_catalog_list" AS mcatli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_catalog_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

