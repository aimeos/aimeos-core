<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_product_property"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_product_property" (
				"parentid", "siteid", "typeid", "langid", "value", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_product_property"
			SET "parentid" = ?, "siteid" = ?, "typeid" = ?, "langid" = ?, "value" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mpropr."id", mpropr."parentid", mpropr."siteid",
				mpropr."typeid", mpropr."langid", mpropr."value",
				mpropr."mtime", mpropr."editor", mpropr."ctime"
			FROM "mshop_product_property" AS mpropr
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
				SELECT DISTINCT mpropr."id"
				FROM "mshop_product_property" AS mpropr
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

