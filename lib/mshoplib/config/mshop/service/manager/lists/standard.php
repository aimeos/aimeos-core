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
				SELECT :key AS "key", mserli."id" AS "id"
				FROM "mshop_service_list" AS mserli
				:joins
				WHERE :cond
				GROUP BY :key, mserli."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_service_list"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_service_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_service_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_service_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_service_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_service_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mserli."id" AS "service.lists.id", mserli."parentid" AS "service.lists.parentid",
				mserli."siteid" AS "service.lists.siteid", mserli."typeid" AS "service.lists.typeid",
				mserli."domain" AS "service.lists.domain", mserli."refid" AS "service.lists.refid",
				mserli."start" AS "service.lists.datestart", mserli."end" AS "service.lists.dateend",
				mserli."config" AS "service.lists.config", mserli."pos" AS "service.lists.position",
				mserli."status" AS "service.lists.status", mserli."mtime" AS "service.lists.mtime",
				mserli."editor" AS "service.lists.editor", mserli."ctime" AS "service.lists.ctime"
			FROM "mshop_service_list" AS mserli
			:joins
			WHERE :cond
			GROUP BY mserli."id", mserli."parentid", mserli."siteid", mserli."typeid",
				mserli."domain", mserli."refid", mserli."start", mserli."end",
				mserli."config", mserli."pos", mserli."status", mserli."mtime",
				mserli."editor", mserli."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mserli."id"
				FROM "mshop_service_list" AS mserli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_service_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

