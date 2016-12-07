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
				SELECT :key AS "key", mtexli."id" AS "id"
				FROM "mshop_text_list" AS mtexli
				:joins
				WHERE :cond
				GROUP BY :key, mtexli."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_text_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_text_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_text_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_text_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_text_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_text_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mtexli."id" AS "text.lists.id", mtexli."parentid" AS "text.lists.parentid",
				mtexli."siteid" AS "text.lists.siteid", mtexli."typeid" AS "text.lists.typeid",
				mtexli."domain" AS "text.lists.domain", mtexli."refid" AS "text.lists.refid",
				mtexli."start" AS "text.lists.datestart", mtexli."end" AS "text.lists.dateend",
				mtexli."config" AS "text.lists.config", mtexli."pos" AS "text.lists.position",
				mtexli."status" AS "text.lists.status", mtexli."mtime" AS "text.lists.mtime",
				mtexli."editor" AS "text.lists.editor", mtexli."ctime" AS "text.lists.ctime"
			FROM "mshop_text_list" AS mtexli
			:joins
			WHERE :cond
			GROUP BY mtexli."id", mtexli."parentid", mtexli."siteid", mtexli."typeid",
				mtexli."domain", mtexli."refid", mtexli."start", mtexli."end",
				mtexli."config", mtexli."pos", mtexli."status", mtexli."mtime",
				mtexli."editor", mtexli."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtexli."id"
				FROM "mshop_text_list" AS mtexli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_text_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

