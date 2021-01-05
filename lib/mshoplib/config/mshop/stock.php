<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


return array(
	'manager' => array(
		'type' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_stock_type"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_stock_type" ( :names
						"code", "domain", "label", "pos", "status",
						"mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_stock_type"
					SET :names
						"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
						"status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mstoty."id" AS "stock.type.id", mstoty."siteid" AS "stock.type.siteid",
						mstoty."code" AS "stock.type.code", mstoty."domain" AS "stock.type.domain",
						mstoty."label" AS "stock.type.label", mstoty."status" AS "stock.type.status",
						mstoty."mtime" AS "stock.type.mtime", mstoty."editor" AS "stock.type.editor",
						mstoty."ctime" AS "stock.type.ctime", mstoty."pos" AS "stock.type.position"
					FROM "mshop_stock_type" mstoty
					:joins
					WHERE :cond
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mstoty."id" AS "stock.type.id", mstoty."siteid" AS "stock.type.siteid",
						mstoty."code" AS "stock.type.code", mstoty."domain" AS "stock.type.domain",
						mstoty."label" AS "stock.type.label", mstoty."status" AS "stock.type.status",
						mstoty."mtime" AS "stock.type.mtime", mstoty."editor" AS "stock.type.editor",
						mstoty."ctime" AS "stock.type.ctime", mstoty."pos" AS "stock.type.position"
					FROM "mshop_stock_type" mstoty
					:joins
					WHERE :cond
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mstoty."id"
						FROM "mshop_stock_type" mstoty
						:joins
						WHERE :cond
						ORDER BY mstoty."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mstoty."id"
						FROM "mshop_stock_type" mstoty
						:joins
						WHERE :cond
						ORDER BY mstoty."id"
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
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_stock"
				WHERE :cond AND siteid = ?
			'
		),
		'insert' => array(
			'ansi' => '
				INSERT INTO "mshop_stock" ( :names
					"prodid", "type", "stocklevel", "backdate",
					"timeframe", "mtime", "editor", "siteid", "ctime"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?, ?
				)
			'
		),
		'update' => array(
			'ansi' => '
				UPDATE "mshop_stock"
				SET :names
					"prodid" = ?, "type" = ?, "stocklevel" = ?, "backdate" = ?,
					"timeframe" = ?, "mtime" = ?, "editor" = ?
				WHERE "siteid" = ? AND "id" = ?
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
					msto."id" AS "stock.id", msto."prodid" AS "stock.productid",
					msto."siteid" AS "stock.siteid", msto."type" AS "stock.type",
					msto."stocklevel" AS "stock.stocklevel", msto."backdate" AS "stock.backdate",
					msto."timeframe" AS "stock.timeframe", msto."mtime" AS "stock.mtime",
					msto."ctime" AS "stock.ctime", msto."editor" AS "stock.editor"
				FROM "mshop_stock" AS msto
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
					msto."id" AS "stock.id", msto."prodid" AS "stock.productid",
					msto."siteid" AS "stock.siteid", msto."type" AS "stock.type",
					msto."stocklevel" AS "stock.stocklevel", msto."backdate" AS "stock.backdate",
					msto."timeframe" AS "stock.timeframe", msto."mtime" AS "stock.mtime",
					msto."ctime" AS "stock.ctime", msto."editor" AS "stock.editor"
				FROM "mshop_stock" AS msto
				:joins
				WHERE :cond
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msto."id"
					FROM "mshop_stock" AS msto
					:joins
					WHERE :cond
					ORDER BY msto."id"
					OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
				) AS list
			',
			'mysql' => '
				SELECT COUNT(*) AS "count"
				FROM (
					SELECT msto."id"
					FROM "mshop_stock" AS msto
					:joins
					WHERE :cond
					ORDER BY msto."id"
					LIMIT 10000 OFFSET 0
				) AS list
			'
		),
		'stocklevel' => array(
			'ansi' => '
				UPDATE "mshop_stock"
				SET "stocklevel" = "stocklevel" - ?, "mtime" = ?, "editor" = ?
				WHERE "prodid" = ? AND "type" = ? AND :cond
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_stock_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
