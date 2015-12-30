<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "madmin_log"
			WHERE :cond
			AND "siteid" = ?
		',
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "madmin_log" (
				"siteid", "facility", "timestamp", "priority", "message", "request"
			) VALUES (
				?, ?, ?, ?, ?, ?
			)
		',
	),
	'update' => array(
		'ansi' => '
			UPDATE "madmin_log"
			SET "siteid" = ?, "facility" = ?, "timestamp" = ?, "priority" = ?,
				"message" = ?, "request" = ?
			WHERE "id" = ?
		',
	),
	'search' => array(
		'ansi' => '
			SELECT malog."id", malog."siteid", malog."facility",
				malog."timestamp", malog."priority", malog."message",
				malog."request"
			FROM "madmin_log" AS malog
			:joins
			WHERE :cond
			GROUP BY malog."id", malog."siteid", malog."facility",
				malog."timestamp", malog."priority", malog."message",
				malog."request" /*-orderby*/, :order /*orderby-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
	',
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT malog."id"
				FROM "madmin_log" AS malog
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		',
	),
	'newid' => array(
		'mysql' => 'SELECT LAST_INSERT_ID()',
	),
);
