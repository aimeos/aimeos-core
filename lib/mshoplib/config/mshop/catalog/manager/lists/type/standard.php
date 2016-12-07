<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_catalog_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_catalog_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_catalog_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcatlity."id" AS "catalog.lists.type.id", mcatlity."siteid" AS "catalog.lists.type.siteid",
				mcatlity."code" AS "catalog.lists.type.code", mcatlity."domain" AS "catalog.lists.type.domain",
				mcatlity."label" AS "catalog.lists.type.label", mcatlity."mtime" AS "catalog.lists.type.mtime",
				mcatlity."editor" AS "catalog.lists.type.editor", mcatlity."ctime" AS "catalog.lists.type.ctime",
				mcatlity."status" AS "catalog.lists.type.status"
			FROM "mshop_catalog_list_type" AS mcatlity
			:joins
			WHERE :cond
			GROUP BY mcatlity."id", mcatlity."siteid", mcatlity."code", mcatlity."domain",
				mcatlity."label", mcatlity."mtime", mcatlity."editor", mcatlity."ctime",
				mcatlity."status" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcatlity."id"
				FROM "mshop_catalog_list_type" AS mcatlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_catalog_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

