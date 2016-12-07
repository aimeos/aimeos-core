<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_service_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_service_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_service_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mserty."id" AS "service.type.id", mserty."siteid" AS "service.type.siteid",
				mserty."domain" AS "service.type.domain", mserty."code" AS "service.type.code",
				mserty."label" AS "service.type.label", mserty."status" AS "service.type.status",
				mserty."mtime" AS "service.type.mtime", mserty."editor" AS "service.type.editor",
				mserty."ctime" AS "service.type.ctime"
			FROM "mshop_service_type" AS mserty
			:joins
			WHERE :cond
			GROUP BY mserty."id", mserty."siteid", mserty."domain", mserty."code",
				mserty."label", mserty."status", mserty."mtime", mserty."editor",
				mserty."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mserty."id"
				FROM "mshop_service_type" AS mserty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_service_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

