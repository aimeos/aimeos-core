<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


return array(
	'manager' => array(
		'delete' => array(
			'ansi' => '
				DELETE FROM "madmin_job"
				WHERE :cond
				AND "siteid" LIKE ?
			',
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "madmin_job" ( :names
					"label", "path", "status", "editor", "mtime", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?
				)
			',
		),
		'update' => array(
			'ansi' => '
				UPDATE "madmin_job"
				SET :names
					"label" = ?, "path" = ?, "status" = ?, "editor" = ?, "mtime" = ?
				WHERE "siteid" LIKE ? AND "id" = ?
			',
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "madmin_job" majob
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "madmin_job" majob
				:joins
				WHERE :cond
				ORDER BY :order
				LIMIT :size OFFSET :start
			',
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT majob."id"
					FROM "madmin_job" majob
					:joins
					WHERE :cond
					ORDER BY "id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM(
					SELECT majob."id"
					FROM "madmin_job" majob
					:joins
					WHERE :cond
					ORDER BY "id"
					LIMIT 10000 OFFSET 0
				) AS list
			',
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT madmin_job_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
