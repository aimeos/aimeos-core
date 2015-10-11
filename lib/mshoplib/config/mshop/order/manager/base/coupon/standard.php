<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mordbaco."id" AS "id"
			FROM "mshop_order_base_coupon" AS mordbaco
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
			DELETE FROM "mshop_order_base_coupon"
			WHERE :cond AND siteid = ?
			'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order_base_coupon" (
				"baseid", "siteid", "ordprodid", "code", "mtime", "editor",
				"ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order_base_coupon"
			SET "baseid" = ?, "siteid" = ?, "ordprodid" = ?, "code" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mordbaco."id", mordbaco."baseid",
				mordbaco."siteid", mordbaco."ordprodid", mordbaco."code",
				mordbaco."mtime", mordbaco."editor", mordbaco."ctime"
			FROM "mshop_order_base_coupon" AS mordbaco
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mordbaco."id" ) AS "count"
			FROM "mshop_order_base_coupon" AS mordbaco
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

