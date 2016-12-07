<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_supplier_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_supplier_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_supplier_list_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT msuplity."id" AS "supplier.lists.type.id", msuplity."siteid" AS "supplier.lists.type.siteid",
				msuplity."code" AS "supplier.lists.type.code", msuplity."domain" AS "supplier.lists.type.domain",
				msuplity."label" AS "supplier.lists.type.label", msuplity."status" AS "supplier.lists.type.status",
				msuplity."mtime" AS "supplier.lists.type.mtime", msuplity."editor" AS "supplier.lists.type.editor",
				msuplity."ctime" AS "supplier.lists.type.ctime"
			FROM "mshop_supplier_list_type" AS msuplity
			:joins
			WHERE :cond
			GROUP BY msuplity."id", msuplity."siteid", msuplity."code", msuplity."domain",
				msuplity."label", msuplity."status", msuplity."mtime", msuplity."editor",
				msuplity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msuplity."id"
				FROM "mshop_supplier_list_type" AS msuplity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_supplier_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

