<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "madmin_job"
			WHERE :cond
			AND "siteid" = ?
		',
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "madmin_job" (
				"siteid", "label", "method", "parameter", "result", "status",
				"editor", "mtime", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		',
	),
	'update' => array(
		'ansi' => '
			UPDATE "madmin_job"
			SET "siteid" = ?, "label" = ?, "method" = ?, "parameter" = ?,
				"result" = ?, "status" = ?, "editor" = ?, "mtime" = ?
			WHERE "id" = ?
		',
	),
	'search' => array(
		'ansi' => '
			SELECT DISTINCT majob."id", majob."siteid", majob."label",
				majob."method", majob."parameter", majob."result", majob."status",
				majob."editor", majob."mtime", majob."ctime"
			FROM "madmin_job" AS majob
			:joins
			WHERE :cond
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		',
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM(
				SELECT DISTINCT majob."id"
				FROM "madmin_job" AS majob
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
