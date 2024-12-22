<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


return array(
	'manager' => array(
		'delete' => array(
			'ansi' => '
				DELETE FROM "mshop_basket"
				WHERE :cond AND "siteid" LIKE ?
			'
		),
		'insert' => array(
			'mysql' => '
				INSERT INTO "mshop_basket" ( :names
					"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?
				) ON DUPLICATE KEY UPDATE
					"customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			',
			'pgsql' => '
				INSERT INTO "mshop_basket" ( :names
					"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
				) VALUES ( :values
					?, ?, ?, ?, ?, ?, ?, ?
				) ON CONFLICT ("id") DO UPDATE SET
					"customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
			',
			'sqlsrv' => '
				MERGE "mshop_basket" AS tgt
				USING ( SELECT ?, ?, ?, ?, ?, ?, ?, ? ) AS src (
					"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
				) ON (tgt."id" = src."id")
				WHEN MATCHED THEN
					UPDATE SET "customerid" = ?, "content" = ?, "name" = ?, "mtime" = ?, "editor" = ?
				WHEN NOT MATCHED THEN
					INSERT ( :names
						"customerid", "content", "name", "mtime", "editor", "siteid", "ctime", "id"
					) VALUES ( :values
						src."customerid", src."content", src."name", src."mtime", src."editor", src."siteid", src."ctime", src."id"
					);
			'
		),
		'search' => array(
			'ansi' => '
				SELECT :columns
				FROM "mshop_basket" mbas
				:joins
				WHERE :cond
				ORDER BY :order
				OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
			',
			'mysql' => '
				SELECT :columns
				FROM "mshop_basket" mbas
				:joins
				WHERE :cond
				ORDER BY :order
				LIMIT :size OFFSET :start
			'
		),
		'count' => array(
			'ansi' => '
				SELECT COUNT( DISTINCT mbas."id" ) AS "count"
				FROM "mshop_basket" mbas
				:joins
				WHERE :cond
			'
		),
		'newid' => array(
			'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
			'mysql' => 'SELECT LAST_INSERT_ID()',
			'oracle' => 'SELECT mshop_basket_seq.CURRVAL FROM DUAL',
			'pgsql' => 'SELECT lastval()',
			'sqlite' => 'SELECT last_insert_rowid()',
			'sqlsrv' => 'SELECT @@IDENTITY',
			'sqlanywhere' => 'SELECT @@IDENTITY',
		),
	),
);
