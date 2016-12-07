<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_attribute_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_attribute_list_type"(
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_attribute_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mattlity."id" AS "attribute.lists.type.id", mattlity."siteid" AS "attribute.lists.type.siteid",
				mattlity."code" AS "attribute.lists.type.code", mattlity."domain" AS "attribute.lists.type.domain",
				mattlity."label" AS "attribute.lists.type.label", mattlity."status" AS "attribute.lists.type.status",
				mattlity."mtime" AS "attribute.lists.type.mtime", mattlity."ctime" AS "attribute.lists.type.ctime",
				mattlity."editor" AS "attribute.lists.type.editor"
			FROM "mshop_attribute_list_type" AS mattlity
			:joins
			WHERE :cond
			GROUP BY mattlity."id", mattlity."siteid", mattlity."code", mattlity."domain",
				mattlity."label", mattlity."status", mattlity."mtime", mattlity."ctime",
				mattlity."editor" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mattlity."id"
				FROM "mshop_attribute_list_type" AS mattlity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_attribute_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

