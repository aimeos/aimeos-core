<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_service_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_service_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_service_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mserlity."id" AS "service.lists.type.id", mserlity."siteid" AS "service.lists.type.siteid",
				mserlity."code" AS "service.lists.type.code", mserlity."domain" AS "service.lists.type.domain",
				mserlity."label" AS "service.lists.type.label", mserlity."status" AS "service.lists.type.status",
				mserlity."mtime" AS "service.lists.type.mtime", mserlity."editor" AS "service.lists.type.editor",
				mserlity."ctime" AS "service.lists.type.ctime"
			FROM "mshop_service_list_type" AS mserlity
			:joins
			WHERE :cond
			GROUP BY mserlity."id", mserlity."siteid", mserlity."code", mserlity."domain",
				mserlity."label", mserlity."status", mserlity."mtime", mserlity."editor",
				mserlity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mserlity."id"
				FROM "mshop_service_list_type" as mserlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_service_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

