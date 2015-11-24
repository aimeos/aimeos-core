<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'aggregate' => array(
		'ansi' => '
			SELECT "key", COUNT("id") AS "count"
			FROM (
				SELECT DISTINCT :key AS "key", msupli."id" AS "id"
				FROM "mshop_supplier_list" AS msupli
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
			DELETE FROM "mshop_supplier_list"
			WHERE :cond AND siteid = ?
		'
	),
	'getposmax' => array(
		'ansi' => '
			SELECT MAX( "pos" ) AS pos
			FROM "mshop_supplier_list"
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_supplier_list" (
				"parentid", "siteid", "typeid", "domain", "refid", "start",
				"end", "config", "pos", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_supplier_list"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "domain" = ?,
				"refid" = ?, "start" = ?, "end" = ?, "config" = ?, "pos" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'updatepos' => array(
		'ansi' => '
			UPDATE "mshop_supplier_list"
			SET "pos" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'move' => array(
		'ansi' => '
			UPDATE "mshop_supplier_list"
			SET "pos" = "pos" + ?, "mtime" = ?, "editor" = ?
			WHERE "siteid" = ? AND "parentid" = ? AND "typeid" = ?
				AND "domain" = ? AND "pos" >= ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT msupli."id" AS "supplier.lists.id", msupli."parentid" AS "supplier.lists.parentid",
				msupli."siteid" AS "supplier.lists.siteid", msupli."typeid" AS "supplier.lists.typeid",
				msupli."domain" AS "supplier.lists.domain", msupli."refid" AS "supplier.lists.refid",
				msupli."start" AS "supplier.lists.datestart", msupli."end" AS "supplier.lists.dateend",
				msupli."config" AS "supplier.lists.config", msupli."pos" AS "supplier.lists.position",
				msupli."status" AS "supplier.lists.status", msupli."mtime" AS "supplier.lists.mtime",
				msupli."editor" AS "supplier.lists.editor", msupli."ctime" AS "supplier.lists.ctime"
			FROM "mshop_supplier_list" AS msupli
			:joins
			WHERE :cond
			 /*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msupli."id"
				FROM "mshop_supplier_list" AS msupli
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

