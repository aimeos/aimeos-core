<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_tag_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_tag_type" (
				"siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_tag_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mtagty."id" AS "tag.type.id", mtagty."siteid" AS "tag.type.siteid",
				mtagty."code" AS "tag.type.code", mtagty."domain" AS "tag.type.domain",
				mtagty."label" AS "tag.type.label", mtagty."status" AS "tag.type.status",
				mtagty."mtime" AS "tag.type.mtime", mtagty."editor" AS "tag.type.editor",
				mtagty."ctime" AS "tag.type.ctime"
			FROM "mshop_tag_type" mtagty
			:joins
			WHERE :cond
			GROUP BY mtagty."id", mtagty."siteid", mtagty."code", mtagty."domain",
				mtagty."label", mtagty."status", mtagty."mtime", mtagty."editor",
				mtagty."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mtagty."id"
				FROM "mshop_tag_type" mtagty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_tag_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

