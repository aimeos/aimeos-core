<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'aggregate' => array(
		'ansi' => '
			SELECT "key", COUNT("id") AS "count"
			FROM (
				SELECT :key AS "key", mprili."id" AS "id"
				FROM "mshop_price_list" AS mprili
				:joins
				WHERE :cond
				GROUP BY :key, mprili."id" /*-columns*/ , :columns /*columns-*/
				/*-orderby*/ ORDER BY :order /*orderby-*/
				LIMIT :size OFFSET :start
			) AS list
			GROUP BY "key"
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_price_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_price_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_price_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_price_list"
			SET "parentid"=?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_price_list"
				SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_price_list"
				SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mprili."id" AS "price.lists.id", mprili."parentid" AS "price.lists.parentid",
				mprili."siteid" AS "price.lists.siteid", mprili."typeid" AS "price.lists.typeid",
				mprili."domain" AS "price.lists.domain", mprili."refid" AS "price.lists.refid",
				mprili."start" AS "price.lists.datestart", mprili."end" AS "price.lists.dateend",
				mprili."config" AS "price.lists.config", mprili."pos" AS "price.lists.position",
				mprili."status" AS "price.lists.status", mprili."mtime" AS "price.lists.mtime",
				mprili."editor" AS "price.lists.editor", mprili."ctime" AS "price.lists.ctime"
			FROM "mshop_price_list" AS mprili
			:joins
			WHERE :cond
			GROUP BY mprili."id", mprili."parentid", mprili."siteid", mprili."typeid",
				mprili."domain", mprili."refid", mprili."start", mprili."end",
				mprili."config", mprili."pos", mprili."status", mprili."mtime",
				mprili."editor", mprili."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mprili."id"
				FROM "mshop_price_list" AS mprili
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_price_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

