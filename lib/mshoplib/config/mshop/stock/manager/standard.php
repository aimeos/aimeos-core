<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

return array(
	'delete' => array(
		'ansi' => '
			DELETE FROM "mshop_stock"
			WHERE :cond AND siteid = ?
		'
	),
	'insert' => array(
		'ansi' => '
			INSERT INTO "mshop_stock" (
				"productcode", "siteid", "typeid", "stocklevel", "backdate",
				"mtime", "editor", "ctime"
			) VALUES (
				?, ?, ?, ?, ?, ?, ?, ?
			)
		'
	),
	'update' => array(
		'ansi' => '
			UPDATE "mshop_stock"
			SET "productcode" = ?, "siteid" = ?, "typeid" = ?,
				"stocklevel" = ?, "backdate" = ?, "mtime" = ?, "editor" = ?
			WHERE "id" = ?
		'
	),
	'search' => array(
		'ansi' => '
			SELECT msto."id" AS "stock.id", msto."productcode" AS "stock.productcode",
				msto."siteid" AS "stock.siteid", msto."typeid" AS "stock.typeid",
				msto."stocklevel" AS "stock.stocklevel", msto."backdate" AS "stock.backdate",
				msto."mtime" AS "stock.mtime", msto."editor" AS "stock.editor",
				msto."ctime" AS "stock.ctime"
			FROM "mshop_stock" AS msto
			:joins
			WHERE :cond
			GROUP BY msto."id", msto."productcode", msto."siteid", msto."typeid",
				msto."stocklevel", msto."backdate", msto."mtime", msto."editor",
				msto."ctime" /*-columns*/ , :columns /*columns-*/
			/*-orderby*/ ORDER BY :order /*orderby-*/
			LIMIT :size OFFSET :start
		'
	),
	'count' => array(
		'ansi' => '
			SELECT COUNT(*) AS "count"
			FROM (
				SELECT DISTINCT msto."id"
				FROM "mshop_stock" AS msto
				:joins
				WHERE :cond
				LIMIT 10000 OFFSET 0
			) AS list
		'
	),
	'stocklevel' => array(
		'ansi' => '
			UPDATE "mshop_stock"
			SET "stocklevel" = "stocklevel" + ?, "mtime" = ?, "editor" = ?
			WHERE :cond
		'
	),
	'newid' => array(
		'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
		'mysql' => 'SELECT LAST_INSERT_ID()',
		'oracle' => 'SELECT mshop_stock_seq.CURRVAL FROM DUAL',
		'pgsql' => 'SELECT lastval()',
		'sqlite' => 'SELECT last_insert_rowid()',
		'sqlsrv' => 'SELECT SCOPE_IDENTITY()',
		'sqlanywhere' => 'SELECT @@IDENTITY',
	),
);

