<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_supplier"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_supplier" (
				"siteid", "code", "label", "status", "mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_supplier"
			SET "siteid" = ?, "code" = ?, "label" = ?, "status" = ?,
				"mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT msup."id" AS "supplier.id", msup."siteid" AS "supplier.siteid",
				msup."code" AS "supplier.code", msup."label" AS "supplier.label",
				msup."status" AS "supplier.status", msup."mtime" AS "supplier.mtime",
				msup."editor" AS "supplier.editor", msup."ctime" AS "supplier.ctime"
			FROM "mshop_supplier" AS msup
			:joins
			WHERE :cond
			GROUP BY msup."id", msup."siteid", msup."code", msup."label",
				msup."status", msup."mtime", msup."editor", msup."ctime"
				/*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msup."id"
				FROM "mshop_supplier" AS msup
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_supplier_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
