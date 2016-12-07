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
				SELECT :key AS "key", mmedli."id" AS "id"
				FROM "mshop_media_list" AS mmedli
				:joins
				WHERE :cond
				GROUP BY :key, mmedli."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_media_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_media_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_media_list"(
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_media_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?,
				"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_media_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_media_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mmedli."id" AS "media.lists.id", mmedli."parentid" AS "media.lists.parentid",
				mmedli."siteid" AS "media.lists.siteid", mmedli."typeid" AS "media.lists.typeid",
				mmedli."domain" AS "media.lists.domain", mmedli."refid" AS "media.lists.refid",
				mmedli."start" AS "media.lists.datestart", mmedli."end" AS "media.lists.dateend",
				mmedli."config" AS "media.lists.config", mmedli."pos" AS "media.lists.position",
				mmedli."status" AS "media.lists.status", mmedli."mtime" AS "media.lists.mtime",
				mmedli."editor" AS "media.lists.editor", mmedli."ctime" AS "media.lists.ctime"
			FROM "mshop_media_list" AS mmedli
			:joins
			WHERE :cond
			GROUP BY mmedli."id", mmedli."parentid", mmedli."siteid", mmedli."typeid",
				mmedli."domain", mmedli."refid", mmedli."start", mmedli."end",
				mmedli."config", mmedli."pos", mmedli."status", mmedli."mtime",
				mmedli."editor", mmedli."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT mmedli."id"
				FROM "mshop_media_list" AS mmedli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_media_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

