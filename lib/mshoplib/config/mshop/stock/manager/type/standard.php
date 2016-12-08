<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2014-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_stock_type"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_stock_type" (
				"siteid", "code", "domain", "label", "status",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_stock_type"
			SET "siteid" = ?, "code" = ?, "domain" = ?, "label" = ?,
				"status" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT mstoty."id" AS "stock.type.id", mstoty."siteid" AS "stock.type.siteid",
				mstoty."code" AS "stock.type.code", mstoty."domain" AS "stock.type.domain",
				mstoty."label" AS "stock.type.label", mstoty."status" AS "stock.type.status",
				mstoty."mtime" AS "stock.type.mtime", mstoty."editor" AS "stock.type.editor",
				mstoty."ctime" AS "stock.type.ctime"
			FROM "mshop_stock_type" mstoty
			:joins
			WHERE :cond
			GROUP BY mstoty."id", mstoty."siteid", mstoty."code", mstoty."domain",
				mstoty."label", mstoty."status", mstoty."mtime", mstoty."editor",
				mstoty."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT mstoty."id"
				FROM "mshop_stock_type" mstoty
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_stock_type_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);
