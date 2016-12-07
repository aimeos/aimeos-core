<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
			SELECT malog."id" AS "log.id", malog."siteid" AS "log.siteid",
				malog."facility" AS "log.facility", malog."timestamp" AS "log.timestamp",
				malog."priority" AS "log.priority", malog."message" AS "log.message",
				malog."request" AS "log.request"
			FROM "madmin_log" AS malog
			:joins
			WHERE :cond
			GROUP BY malog."id", malog."siteid", malog."facility",
				malog."timestamp", malog."priority", malog."message",
				malog."request" /*-columns*/ , :columns /*columns-*/
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
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT madmin_log_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
