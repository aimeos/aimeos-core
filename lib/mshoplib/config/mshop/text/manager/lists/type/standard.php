<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_text_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_text_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_text_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mtexlity."id" AS "text.lists.type.id", mtexlity."siteid" AS "text.lists.type.siteid",
				mtexlity."code" AS "text.lists.type.code", mtexlity."domain" AS "text.lists.type.domain",
				mtexlity."label" AS "text.lists.type.label", mtexlity."status" AS "text.lists.type.status",
				mtexlity."mtime" AS "text.lists.type.mtime", mtexlity."editor" AS "text.lists.type.editor",
				mtexlity."ctime" AS "text.lists.type.ctime"
			FROM "mshop_text_list_type" AS mtexlity
			:joins
			WHERE :cond
			GROUP BY mtexlity."id", mtexlity."siteid", mtexlity."code", mtexlity."domain",
				mtexlity."label", mtexlity."status", mtexlity."mtime", mtexlity."editor",
				mtexlity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtexlity."id"
				FROM "mshop_text_list_type" as mtexlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_text_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

