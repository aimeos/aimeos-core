<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_attribute_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_attribute_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_attribute_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mattty."id" AS "attribute.type.id", mattty."siteid" AS "attribute.type.siteid",
				mattty."code" AS "attribute.type.code", mattty."domain" AS "attribute.type.domain",
				mattty."label" AS "attribute.type.label", mattty."status" AS "attribute.type.status",
				mattty."mtime" AS "attribute.type.mtime", mattty."ctime" AS "attribute.type.ctime",
				mattty."editor" AS "attribute.type.editor"
			FROM "mshop_attribute_type" AS mattty
			:joins
			WHERE :cond
			GROUP BY mattty."id", mattty."siteid", mattty."code", mattty."domain",
				mattty."label", mattty."status", mattty."mtime", mattty."ctime",
				mattty."editor" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattty."id"
				FROM "mshop_attribute_type" AS mattty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_attribute_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

