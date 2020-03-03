<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


return array(
	'manager' => array(
		'type' => array(
			'standard' => array(
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
						SELECT DISTINCT :columns
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
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
		),
		'standard' => array(
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_stock"
					WHERE :cond AND siteid = ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_stock" ( :names
						"productcode", "type", "stocklevel", "backdate",
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
						"productcode" = ?, "type" = ?, "stocklevel" = ?, "backdate" = ?,
						"timeframe" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT DISTINCT :columns
						msto."id" AS "stock.id", msto."productcode" AS "stock.productcode",
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
					SET "stocklevel" = "stocklevel" - ?, "mtime" = ?, "editor" = ?
					WHERE "productcode" = ? AND "type" = ? AND :cond
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
	),
);
