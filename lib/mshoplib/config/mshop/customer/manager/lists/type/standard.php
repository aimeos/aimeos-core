<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_customer_list_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_customer_list_type" (
				"siteid", "code", "domain", "label", "status", "mtime",
				"editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_customer_list_type"
			SET "siteid"=?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mcuslity."id" AS "customer.lists.type.id", mcuslity."siteid" AS "customer.lists.type.siteid",
				mcuslity."code" AS "customer.lists.type.code", mcuslity."domain" AS "customer.lists.type.domain",
				mcuslity."label" AS "customer.lists.type.label", mcuslity."status" AS "customer.lists.type.status",
				mcuslity."mtime" AS "customer.lists.type.mtime", mcuslity."editor" AS "customer.lists.type.editor",
				mcuslity."ctime" AS "customer.lists.type.ctime"
			FROM "mshop_customer_list_type" AS mcuslity
			:joins
			WHERE :cond
			GROUP BY mcuslity."id", mcuslity."siteid", mcuslity."code", mcuslity."domain",
				mcuslity."label", mcuslity."status", mcuslity."mtime", mcuslity."editor",
				mcuslity."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mcuslity."id"
				FROM "mshop_customer_list_type" as mcuslity
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS LIST
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_customer_list_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

