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
				SELECT :key AS "key", mattli."id" AS "id"
				FROM "mshop_attribute_list" AS mattli
				:joins
				WHERE :cond
				GROUP BY :key, mattli."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_attribute_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_attribute_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_attribute_list"(
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_attribute_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_attribute_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_attribute_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mattli."id" AS "attribute.lists.id", mattli."siteid" AS "attribute.lists.siteid",
				mattli."parentid" AS "attribute.lists.parentid", mattli."typeid" AS "attribute.lists.typeid",
				mattli."domain" AS "attribute.lists.domain", mattli."refid" AS "attribute.lists.refid",
				mattli."start" AS "attribute.lists.datestart", mattli."end" AS "attribute.lists.dateend",
				mattli."config" AS "attribute.lists.config", mattli."pos" AS "attribute.lists.position",
				mattli."status" AS "attribute.lists.status", mattli."mtime" AS "attribute.lists.mtime",
				mattli."ctime" AS "attribute.lists.ctime", mattli."editor" AS "attribute.lists.editor"
			FROM "mshop_attribute_list" AS mattli
			:joins
			WHERE :cond
			GROUP BY mattli."id", mattli."siteid", mattli."parentid", mattli."typeid",
				mattli."domain", mattli."refid", mattli."start", mattli."end",
				mattli."config", mattli."pos", mattli."status", mattli."mtime",
				mattli."ctime", mattli."editor" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattli."id"
				FROM "mshop_attribute_list" AS mattli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_attribute_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

