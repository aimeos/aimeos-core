<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'aggregate' => array(
		'ansi' => '
		SELECT "key", COUNT("id") AS "count"
		FROM (
			SELECT DISTINCT :key AS "key", mord."id" AS "id"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		) AS list
		GROUP BY "key"
	'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_order" (
				"baseid", "siteid", "type", "datepayment", "datedelivery",
				"statusdelivery", "statuspayment", "relatedid", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_order"
			SET "baseid" = ?, "siteid" = ?, "type" = ?, "datepayment" = ?,
				"datedelivery" = ?, "statusdelivery" = ?, "statuspayment" = ?,
				"relatedid" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_order"
			WHERE :cond AND siteid = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT mord."id", mord."baseid", mord."siteid",
				mord."type", mord."datepayment", mord."datedelivery",
				mord."statuspayment", mord."statusdelivery", mord."relatedid",
				mord."ctime", mord."mtime", mord."editor"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
			/*-orderby*/ORDER BY :order/*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT( DISTINCT mord."id" ) AS "count"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
		'
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()'
	),
);

