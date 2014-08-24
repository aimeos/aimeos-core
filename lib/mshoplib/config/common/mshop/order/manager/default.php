<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

return array(
	'aggregate' => '
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
	',
	'item' => array(
		'insert' => '
			INSERT INTO "mshop_order" (
				"baseid", "siteid", "type", "datepayment", "datedelivery",
				"statusdelivery", "statuspayment", "relatedid", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
		'update' => '
			UPDATE "mshop_order"
			SET "baseid" = ?, "siteid" = ?, "type" = ?, "datepayment" = ?,
				"datedelivery" = ?, "statusdelivery" = ?, "statuspayment" = ?,
				"relatedid" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		',
		'delete' => '
			DELETE FROM "mshop_order"
			WHERE :cond AND siteid = ?
		',
		'search' => '
			SELECT DISTINCT mord."id", mord."baseid", mord."siteid",
				mord."type", mord."datepayment", mord."datedelivery",
				mord."statuspayment", mord."statusdelivery", mord."relatedid",
				mord."ctime", mord."mtime", mord."editor"
			FROM "mshop_order" AS mord
			:joins
			WHERE :cond
			/*-orderby*/ORDER BY :order/*orderby-*/
			LIMIT :size OFFSET :start
		',
		'count' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mord."id"
				FROM "mshop_order" AS mord
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
);
