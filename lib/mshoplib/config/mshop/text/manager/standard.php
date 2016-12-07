<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_text"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_text" (
				"siteid", "langid", "typeid", "domain", "label", "content",
				"status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_text"
			SET "siteid" = ?, "langid" = ?, "typeid" = ?, "domain" = ?,
				"label" = ?, "content" = ?, "status" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mtex."id" AS "text.id", mtex."siteid" AS "text.siteid",
				mtex."langid" AS "text.languageid",	mtex."typeid" AS "text.typeid",
				mtex."domain" AS "text.domain", mtex."label" AS "text.label",
				mtex."content" AS "text.content", mtex."status" AS "text.status",
				mtex."mtime" AS "text.mtime", mtex."editor" AS "text.editor",
				mtex."ctime" AS "text.ctime"
			FROM "mshop_text" AS mtex
			:joins
			WHERE :cond
			GROUP BY mtex."id", mtex."siteid", mtex."langid",	mtex."typeid",
				mtex."domain", mtex."label", mtex."content", mtex."status",
				mtex."mtime", mtex."editor", mtex."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtex."id"
				FROM "mshop_text" AS mtex
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_text_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

