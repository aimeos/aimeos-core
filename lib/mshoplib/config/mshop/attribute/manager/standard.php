<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_attribute"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_attribute" (
				"siteid", "typeid", "domain", "code", "status", "pos", "label",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_attribute"
			SET "siteid" = ?, "typeid" = ?, "domain" = ?, "code" = ?,
				"status" = ?, "pos" = ?, "label" = ?, "mtime" = ?,
				"editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT matt."id" AS "attribute.id", matt."siteid" AS "attribute.siteid",
				matt."typeid" AS "attribute.typeid", matt."domain" AS "attribute.domain",
				matt."code" AS "attribute.code", matt."status" AS "attribute.status",
				matt."pos" AS "attribute.position", matt."label" AS "attribute.label",
				matt."mtime" AS "attribute.mtime", matt."ctime" AS "attribute.ctime",
				matt."editor" AS "attribute.editor"
			FROM "mshop_attribute" AS matt
			:joins
			WHERE :cond
			GROUP BY matt."id", matt."siteid", matt."typeid", matt."domain",
				matt."code", matt."status", matt."pos", matt."label",
				matt."mtime", matt."ctime", matt."editor" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT matt."id"
				FROM "mshop_attribute" AS matt
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_attribute_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

