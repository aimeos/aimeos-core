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
				SELECT DISTINCT :key AS "key", mproli."id" AS "id"
				FROM "mshop_product_list" AS mproli
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
			DELETE FROM "mshop_product_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_product_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_product_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_product_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mproli."id" AS "product.lists.id", mproli."parentid" AS "product.lists.parentid",
				mproli."siteid" AS "product.lists.siteid", mproli."typeid" AS "product.lists.typeid",
				mproli."domain" AS "product.lists.domain", mproli."refid" AS "product.lists.refid",
				mproli."start" AS "product.lists.datestart", mproli."end" AS "product.lists.dateend",
				mproli."config" AS "product.lists.config", mproli."pos" AS "product.lists.position",
				mproli."status" AS "product.lists.status", mproli."mtime" AS "product.lists.mtime",
				mproli."editor" AS "product.lists.editor", mproli."ctime" AS "product.lists.ctime"
			FROM "mshop_product_list" AS mproli
			:joins
			WHERE :cond
			GROUP BY mproli."id", mproli."parentid", mproli."siteid", mproli."typeid",
				mproli."domain", mproli."refid", mproli."start", mproli."end",
				mproli."config", mproli."pos", mproli."status", mproli."mtime",
				mproli."editor", mproli."ctime" /*-columns*/ , :columns /*columns-*/
			 /*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mproli."id"
				FROM "mshop_product_list" AS mproli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_product_list_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

